<?php


namespace App\Entities;


class Order
{
    public $header;
    public $lines;

    public function __construct(int $header, array $lines = [])
    {
        $this->header = $header;
        $this->lines = $lines;
    }
}
