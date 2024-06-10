<?php

namespace App\Order\Application\ViewModel;

use App\Order\Application\Command\ProductCommand;

class OrderItemView
{
    private int $id;
    private ProductCommand $product;
    private int $quantity = 0;
    private int $unitPrice;
    private int $taxValue;
    private int $total = 0;
    private int $discount;
    private int $discountValue;
    private int $distributedOrderDiscountValue;
    private int $discountedUnitPrice;
    private float $rate;

    public function __construct(
        int            $id,
        ProductCommand $product,
        int            $unitPrice,
        int            $discount,
        int            $discountValue,
        int            $distributedOrderDiscountValue,
        int            $discountedUnitPrice,
        int            $quantity,
        int            $total,
        int            $taxValue,
        float            $rate
    )
    {
        $this->id = $id;
        $this->product = $product;
        $this->unitPrice = $unitPrice;
        $this->discount = $discount;
        $this->discountValue = $discountValue;
        $this->distributedOrderDiscountValue = $distributedOrderDiscountValue;
        $this->discountedUnitPrice = $discountedUnitPrice;
        $this->quantity = $quantity;
        $this->total = $total;
        $this->taxValue = $taxValue;
        $this->rate = $rate;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "product" => $this->product->toArray(),
            "unitPrice" => (int) round($this->unitPrice * $this->rate),
            "discount" => $this->discount,
            "discountValue" => (int) round($this->discountValue * $this->rate),
            "distributedOrderDiscountValue" => (int) round($this->distributedOrderDiscountValue * $this->rate),
            "discountedUnitPrice" => (int) round($this->discountedUnitPrice * $this->rate),
            "quantity" => $this->quantity,
            "total" => (int) round($this->total * $this->rate),
            "taxValue" => (int) round($this->taxValue * $this->rate),
        ];
    }
}