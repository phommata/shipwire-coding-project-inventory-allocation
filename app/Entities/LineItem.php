<?php


namespace App\Entities;


class LineItem
{
    public $name;
    public $quantity;

    public function __construct(string $name, int $quantity)
    {
        $this->name = $name;
        $this->quantity = $quantity;
    }
}
