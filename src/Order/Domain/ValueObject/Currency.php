<?php

namespace App\Order\Domain\ValueObject;

class Currency
{
    public const PLN = "PLN";
    public const EUR = "EUR";
    public static function getValues(): array {
        return [
            self::PLN => 1,
            self::EUR => 0.23
        ];
    }
}