<?php


namespace App\Entities;


class Order
{
    public $header;
    public $lines;
    public $isValid;

    public function __construct(int $header, array $lines = [], bool $isValid)
    {
        $this->header = $header;
        $this->lines = $lines;
        $this->isValid = $isValid;
    }
}
