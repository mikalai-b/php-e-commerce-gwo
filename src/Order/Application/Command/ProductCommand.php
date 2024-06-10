<?php

namespace App\Order\Application\Command;

class ProductCommand
{
    private string $code;
    private string $name;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            "code" => $this->code,
            "name" => $this->name,
        ];
    }

}