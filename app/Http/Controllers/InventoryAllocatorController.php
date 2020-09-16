<?php


namespace App\Http\Controllers;

use App\Builder\InventoryAllocatorResponseBuilder;
use App\Inventory;
use App\Mapper\Request\OrderStreamRequestMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InventoryAllocatorController extends Controller
{
    const ORDER_QTY_ZERO = 0;
    const ORDER_QTY_MAX = 5;

    public function update(Request $request, OrderStreamRequestMapper $orderStreamRequestMapper, InventoryAllocatorResponseBuilder $inventoryAllocatorResponseBuilder)
    {
        $this->validate($request, [
            '*' => 'required|array|min:1',
            '*.Header' => 'required|integer',
            '*.Lines' => 'required|array|min:1',
            '*.Lines.*.Product' => 'required|string',
            '*.Lines.*.Quantity' => 'required|integer',
        ]);

        $orderStream = $orderStreamRequestMapper->mapRequest($request->input());

        if (empty($orderStream)) {
            return response()->json(["error" => "There was an error mapping order stream. Please check the request."], Response::HTTP_BAD_REQUEST);
        }

        $this->handleUpdate($inventoryAllocatorResponseBuilder, $orderStream);

        // Respond with stream of original order, allocated, back ordered
        return response()->json($inventoryAllocatorResponseBuilder->response);
    }

    /**
     * @param InventoryAllocatorResponseBuilder $inventoryAllocatorResponseBuilder
     * @param array $orderStream
     */
    private function handleUpdate(InventoryAllocatorResponseBuilder $inventoryAllocatorResponseBuilder, array $orderStream): void
    {
        // Loop through stream of orders
        foreach ($orderStream as $order) {
            Log::debug("\$order: " . print_r($order, true));

            $inventoryOrder = [];
            $allocateOrder = [];
            $backOrder = [];

            // get current inventories
            $inventories = Inventory::all();
            $this->processInventoryOrders($inventories, $order, $inventoryOrder, $allocateOrder, $backOrder);

            $inventoryAllocatorResponseBuilder->buildResponse([
                'Header' => $order->header,
                'Order' => $inventoryOrder,
                'Allocate' => $allocateOrder,
                'BackOrder' => $backOrder,
            ]);
        }
    }

    /**
     * @param $inventories
     * @param $order
     * @param array $inventoryOrder
     * @param array $allocateOrder
     * @param array $backOrder
     * @return array
     */
    private function processInventoryOrders($inventories, $order, array &$inventoryOrder, array &$allocateOrder, array &$backOrder)
    {
        // loop through inventories
        foreach ($inventories as $inventory) {
            $inventoryRequested = false;
            Log::debug("\$inventory->name: $inventory->name requested init state $inventoryRequested");

            // loop through lines
            foreach ($order->lines as $lineItem) {
                Log::debug("\$lineItem: " . print_r($lineItem, true));

                // check invalid inventory allocation order, qty > 5 or not at least 1 qty ordered
                if ($lineItem->name == $inventory['name'] && $lineItem->quantity > self::ORDER_QTY_ZERO && $lineItem->quantity <= self::ORDER_QTY_MAX) {
                    Log::debug("valid line item: ");

                    // inventory ordered
                    $inventoryOrder[] = [
                        'Product' => $inventory->name,
                        'Quantity' => $lineItem->quantity,
                    ];

                    $invItem = Inventory::where('name', $lineItem->name)->first();

                    // Update inventory for each order until 0; back order any inventory
                    if ($lineItem->quantity <= $inventory->quantity) {
                        $this->allocateSufficientInventory($allocateOrder, $backOrder, $inventory, $lineItem, $invItem, $inventoryRequested);

                    } elseif ($lineItem->quantity > $inventory->quantity) {
                        $this->allocateInsufficientInventory($allocateOrder, $backOrder, $inventory, $lineItem, $invItem, $inventoryRequested);
                    }

                    $invItem->save();
                }
            }

            Log::debug("\$inventory->name: $inventory->name before not requested");

            $this->inventoryNotRequested($inventoryOrder, $allocateOrder, $backOrder, $inventoryRequested, $inventory);

            Log::debug("\$allocateOrder: " . print_r($allocateOrder, true));
        }
    }

    /**
     * @param array $allocateOrder
     * @param array $backOrder
     * @param $inventory
     * @param $lineItem
     * @param $invItem
     * @param $inventoryRequested
     * @return array
     */
    private function allocateSufficientInventory(array &$allocateOrder, array &$backOrder, $inventory, $lineItem, $invItem, &$inventoryRequested): void
    {
        Log::debug("{$inventory['name']} enough quantity {$lineItem->quantity} <= {$inventory->quantity}");

        $inventoryRequested = true;
        Log::debug("\$inventory->name: $inventory->name requested true state $inventoryRequested");

        // allocate
        $allocateOrder[] = [
            'Product' => $lineItem->name,
            'Quantity' => $lineItem->quantity,
        ];

        // back order
        $backOrder[] = [
            'Product' => $inventory->name,
            'Quantity' => self::ORDER_QTY_ZERO,
        ];

        // deduct current inventory item
        $invItem->quantity -= $lineItem->quantity;
    }

    /**
     * @param array $allocateOrder
     * @param array $backOrder
     * @param $inventory
     * @param $lineItem
     * @param $invItem
     * @param $inventoryRequested
     * @return array
     */
    private function allocateInsufficientInventory(array &$allocateOrder, array &$backOrder, $inventory, $lineItem, $invItem, &$inventoryRequested): void
    {
        Log::debug("{$inventory['name']} just enough quantity: {$lineItem->quantity} > {$inventory->quantity}");

        $inventoryRequested = true;
        Log::debug("\$inventory->name: $inventory->name requested true state $inventoryRequested");

        // deduct quantity until 0
        $allocateOrder[] = [
            'Product' => $lineItem->name,
            'Quantity' => $inventory->quantity,
        ];

        Log::debug("\$allocateOrder: " . print_r($allocateOrder, true));

        // back order
        $backOrder[] = [
            'Product' => $lineItem->name,
            'Quantity' => $lineItem->quantity - $inventory->quantity,
        ];

        // deduct current inventory item
        $invItem->quantity = self::ORDER_QTY_ZERO;
    }

    /**
     * @param array $inventoryOrder
     * @param array $allocateOrder
     * @param array $backOrder
     * @param bool $inventoryRequested
     * @param $inventory
     */
    private function inventoryNotRequested(array &$inventoryOrder, array &$allocateOrder, array &$backOrder, bool $inventoryRequested, $inventory): void
    {
        if (!$inventoryRequested) {
            Log::debug("\$inventory->name: $inventory->name not requested");

            // inventory ordered
            $inventoryOrder[] = [
                'Product' => $inventory->name,
                'Quantity' => self::ORDER_QTY_ZERO,
            ];

            // allocate
            $allocateOrder[] = [
                'Product' => $inventory->name,
                'Quantity' => self::ORDER_QTY_ZERO,
            ];

            // back order
            $backOrder[] = [
                'Product' => $inventory->name,
                'Quantity' => self::ORDER_QTY_ZERO,
            ];
        }
    }
}
