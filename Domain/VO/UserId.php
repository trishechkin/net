<?php

namespace Domain\VO;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UserId
{
    private function __construct(
        private readonly UuidInterface $uuid
    )
    {
    }

    public static function make(?string $uuid = null): self
    {
        if ($uuid === null) {
            return new static(Uuid::uuid4());
        }

        return new static(Uuid::fromString($uuid));
    }

    public function getValue(): string
    {
        return $this->uuid->toString();
    }
}
