<?php


namespace App\Builder;


class InventoryAllocatorResponseBuilder
{
    public $response;

    public function buildResponse(array $data)
    {
        $this->response[] = $data;
    }
}
