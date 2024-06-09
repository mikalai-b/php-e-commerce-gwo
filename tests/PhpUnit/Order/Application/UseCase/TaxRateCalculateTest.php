<?php

namespace App\Tests\PhpUnit\Order\Application\UseCase;


use App\Order\Application\UseCase\TaxRateCalculate;
use App\Product\Domain\Model\Product;
use PHPUnit\Framework\TestCase;

class TaxRateCalculateTest extends TestCase
{
    private TaxRateCalculate $case;
    public function setUp(): void
    {
        $this->case = new TaxRateCalculate();
    }

    public function testNotNullExecute(): void
    {
        $product = $this->createMock(Product::class);
        $product->expects($this->once())->method('getTaxRate')->willReturn(10);
        $subtotal = 100;

        $this->assertEquals(10, $this->case->execute($product, $subtotal));
    }

    public function testNullExecute(): void
    {
        $product = $this->createMock(Product::class);
        $product->expects($this->once())->method('getTaxRate')->willReturn(null);
        $subtotal = 100;

        $this->assertEquals(0, $this->case->execute($product, $subtotal));
    }
}
