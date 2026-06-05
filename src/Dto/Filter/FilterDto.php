<?php

namespace Wertelko\EasyadminContentBundle\Dto\Filter;

class FilterDto
{
    public function __construct(
        private readonly array $filters = [],
    )
    {
    }

    public function get(string $name, $default = null): mixed
    {
        return $this->filters[$name] ?? $default;
    }
}