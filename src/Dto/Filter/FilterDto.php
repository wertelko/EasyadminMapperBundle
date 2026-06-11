<?php

namespace Wertelko\EasyadminMapperBundle\Dto\Filter;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class FilterDto implements IteratorAggregate, Countable
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

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->filters);
    }

    public function count(): int
    {
        return count($this->filters);
    }
}