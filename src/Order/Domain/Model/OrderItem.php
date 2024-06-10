<?php

declare(strict_types=1);

namespace App\Order\Domain\Model;

use App\Product\Domain\Model\Product;

class OrderItem
{
    const MAX_QUANTITY = 99;

    protected int $id;
    protected ?Order $order;
    protected ?Product $product;
    protected int $quantity = 0;
    protected int $unitPrice;
    protected ?int $taxValue;
    protected int $total = 0;
    protected int $discount;
    protected int $discountValue;
    protected int $distributedOrderDiscountValue;
    protected int $discountedUnitPrice;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getSubtotal(): int
    {
        return $this->unitPrice * $this->quantity;
    }

    public function getTaxValue(): ?int
    {
        return $this->taxValue;
    }

    public function setTaxValue(?int $taxValue): void
    {
        $this->taxValue = $taxValue;
    }

    public function recalculateTotal(): void
    {
        $this->total = $this->getSubtotal();
    }

    /**
     * @return int
     */
    public function getDiscount(): int
    {
        return $this->discount;
    }

    /**
     * @param int $discount
     */
    public function setDiscount(int $discount): void
    {
        $this->discount = $discount;
    }

    /**
     * @return int
     */
    public function getDiscountValue(): int
    {
        return $this->discountValue;
    }

    /**
     * @param int $discountValue
     */
    public function setDiscountValue(int $discountValue): void
    {
        $this->discountValue = $discountValue;
    }

    /**
     * @return int
     */
    public function getDistributedOrderDiscountValue(): int
    {
        return $this->distributedOrderDiscountValue;
    }

    /**
     * @param int $distributedOrderDiscountValue
     */
    public function setDistributedOrderDiscountValue(int $distributedOrderDiscountValue): void
    {
        $this->distributedOrderDiscountValue = $distributedOrderDiscountValue;
    }

    /**
     * @return int
     */
    public function getDiscountedUnitPrice(): int
    {
        return $this->discountedUnitPrice;
    }

    /**
     * @param int $discountedUnitPrice
     */
    public function setDiscountedUnitPrice(int $discountedUnitPrice): void
    {
        $this->discountedUnitPrice = $discountedUnitPrice;
    }
}
