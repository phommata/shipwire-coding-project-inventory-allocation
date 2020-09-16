<?php


namespace App\Mapper\Request;


use App\Entities\LineItem;
use App\Entities\Order;
use Illuminate\Support\Facades\Log;

class OrderStreamRequestMapper
{
    public function mapRequest(array $data)
    {
        $orderStream = [];

        try {
            foreach ($data as $order) {
                $line = [];
                foreach ($order['Lines'] as $lineItem) {
                    Log::debug("\$lineItem: " . print_r($lineItem, true));

                    $lineItem = new LineItem($lineItem['Product'], $lineItem['Quantity']);
                    $line[] = $lineItem;
                }

                $orderStream[] = new Order($order['Header'], $line);
            }
        } catch (\Exception $exception) {
            Log::error("eror mapping \$orderStream: " . print_r($orderStream, true));
        }

        Log::debug("\$orderStream: " . print_r($orderStream, true));

        return $orderStream;
    }
}
