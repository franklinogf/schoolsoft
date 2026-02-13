<?php

namespace App\Dtos;

class StoreItemOption
{
    public function __construct(
        public string $name,
        public ?float $price = null,
        public int $order = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            price: isset($data['price']) ? (float) $data['price'] : null,
            order: (int) ($data['order'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'order' => $this->order,
        ];
    }
}
