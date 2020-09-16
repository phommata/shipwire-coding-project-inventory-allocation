<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class InventoryAllocatorControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function index_responds_with_200_status_code()
    {
        $this->get('/')->seeStatusCode(Response::HTTP_OK);
    }

    /** @test */
    public function update_can_allocate_an_existing_inventory()
    {
        factory(\App\Inventory::class)->create([
            'name' => 'A',
            'quantity' => 2
        ])
            ->save();

        factory(\App\Inventory::class)->create([
            'name' => 'B',
            'quantity' => 3
        ])
            ->save();

        factory(\App\Inventory::class)->create([
            'name' => 'C',
            'quantity' => 1
        ])
            ->save();

        factory(\App\Inventory::class)->create([
            'name' => 'D',
            'quantity' => 0
        ])
            ->save();

        factory(\App\Inventory::class)->create([
            'name' => 'E',
            'quantity' => 0
        ])
            ->save();

        $requestData = [
            [
                'Header' => 1,
                'Lines' => [
                    [
                        'Product' => 'A',
                        'Quantity' => 1
                    ],
                    [
                        'Product' => 'C',
                        'Quantity' => 1
                    ],
                ]
            ],
            [
                'Header' => 2,
                'Lines' => [
                    [
                        'Product' => 'E',
                        'Quantity' => 5
                    ]
                ]
            ],
            [
                'Header' => 3,
                'Lines' => [
                    [
                        'Product' => 'D',
                        'Quantity' => 4
                    ]
                ]
            ],
            [
                'Header' => 4,
                'Lines' => [
                    [
                        'Product' => 'A',
                        'Quantity' => 1
                    ],
                    [
                        'Product' => 'C',
                        'Quantity' => 1
                    ],
                ]
            ],
            [
                'Header' => 5,
                'Lines' => [
                    [
                        'Product' => 'B',
                        'Quantity' => 3
                    ]
                ],
            ],
            [
                'Header' => 6,
                'Lines' => [
                    [
                        'Product' => 'D',
                        'Quantity' => 4
                    ]
                ],
            ]
        ];

        $this
            ->put(
                "/api/v1/inventoryAllocator/update",
                $requestData,
                ['Accept' => 'application/json']
            )
            ->seeStatusCode(200)
            ->seeJson([
                [
                    'Header' => 1,
                    'Order' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'Allocate' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'BackOrder' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ]
                ],
                [
                    'Header' => 2,
                    'Order' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'Allocate' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'BackOrder' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 5
                        ],
                    ]
                ],
                [
                    'Header' => 3,
                    'Order' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 4
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'Allocate' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'BackOrder' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 4
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ]
                ],
                [
                    'Header' => 4,
                    'Order' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'Allocate' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'BackOrder' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ]
                ],
                [
                    'Header' => 1,
                    'Order' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'Allocate' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'BackOrder' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 1
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ]
                ],
                [
                    'Header' => 6,
                    'Order' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 4
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'Allocate' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 4
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ],
                    'BackOrder' => [
                        [
                            'Product' => 'A',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'B',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'C',
                            'Quantity' => 0
                        ],
                        [
                            'Product' => 'D',
                            'Quantity' => 4
                        ],
                        [
                            'Product' => 'E',
                            'Quantity' => 0
                        ],
                    ]
                ],
            ])
            ->seeInDatabase('inventories', [
                'name' => 'A',
                'quantity' => 0,
            ])
            ->seeInDatabase('inventories', [
                'name' => 'B',
                'quantity' => 0,
            ])
            ->seeInDatabase('inventories', [
                'name' => 'C',
                'quantity' => 0,
            ])
            ->seeInDatabase('inventories', [
                'name' => 'D',
                'quantity' => 0,
            ])
            ->seeInDatabase('inventories', [
                'name' => 'B',
                'quantity' => 0,
            ])
            ->notSeeInDatabase('inventories', [
                'name' => 'F',
                'quantity' => 0,
            ]);

        $this->assertArrayHasKey('data', $this->response->getData(true));
    }

    /** @test */
    public function validation_validates_required_fields()
    {
        $tests = [
            ['method' => 'put', 'url' => "/api/v1/inventoryAllocator/update"],
        ];

        foreach ($tests as $test) {
            $method = $test['method'];
            $this->$method($test['url'], [], ['Accept' => 'application/json']);
            $this->seeStatusCode(Response::HTTP_BAD_REQUEST);

            $data = $this->response->getData(true);

            $this->assertArrayHasKey('error', $data);
            $this->assertEquals("There was an error mapping order stream. Please check the request.", $data['error']);
        }
    }


    /**
     * Provides boilerplate test instructions for validation.
     *
     * @return array
     */
    private function getValidationTestData()
    {
        $inventory = factory(\App\Inventory::class)->create();

        return [
            // Update
            [
                'method' => 'put',
                'url' => "/api/v1/inventoryAllocator/update",
                'status' => 200,
                'data' =>
                    [
                        "Header" => 1,
                        "Lines" => [
                            [
                                'Product' => 'A',
                                'Quantity' => 1
                            ],
                            [
                                'Product' => 'C',
                                'Quantity' => 4
                            ],
                        ]
                    ],
            ]
        ];
    }
    /** @test */
    public function validation_invalidates_incorrect_quantity_data()
    {
        foreach ($this->getValidationTestData() as $test) {
            $method = $test['method'];
            $test['data']['quantity'] = 'unknown';
            $this->$method($test['url'], $test['data'], ['Accept' => 'application/json']);

            $this->seeStatusCode(422);

            $data = $this->response->getData(true);
            $this->assertCount(8, $data);
            $this->assertArrayHasKey('quantity', $data);
        }
    }
}
