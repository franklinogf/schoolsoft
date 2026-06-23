<?php

namespace App\Dtos;

use App\Enums\AppointmentMemberEnum;

final readonly class  FamilyMember
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public AppointmentMemberEnum $relationship,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'relationship' => $this->relationship->value,
        ];
    }
}
