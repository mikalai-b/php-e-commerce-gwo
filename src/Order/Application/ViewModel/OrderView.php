<?php

namespace App\Order\Application\ViewModel;

class OrderView
{
    private int $id;
    private int $itemsTotal;
    private int $taxTotal;
    private int $total;
    private array $items;
    private float $rate;
    public function __construct(
        int $id,
        int $itemsTotal,
        int $taxTotal,
        int $total,
        array $items,
        float $rate
    )
    {
        $this->id = $id;
        $this->itemsTotal = $itemsTotal;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
        $this->items = $items;
        $this->rate = $rate;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "itemsTotal" => $this->itemsTotal,
            "taxTotal" => (int) round($this->taxTotal * $this->rate),
            "total" => (int) round($this->total * $this->rate),
            "items" => $this->items
        ];
    }
}