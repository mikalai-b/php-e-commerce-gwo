<?php

namespace App\Order\Application\UseCase;


use App\Product\Domain\Model\Product;

class TaxRateCalculate
{
    public function execute(Product $product, int $subtotal): int
    {
        $taxRate = $product->getTaxRate();
        if (is_null($taxRate)) {
            return 0;
        }

        return (int) ($subtotal * $taxRate/100);
    }
}