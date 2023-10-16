<?php

namespace Infrastructure\Support;

class CommandMap
{
    /**
     * @var string[]
     */
    private array $map = [];

    public function set(string $name, string $value): void
    {
        $this->map[$name] = $value;
    }

    /**
     * @return string[]
     */
    public function getMap(): array
    {
        return $this->map;
    }
}
