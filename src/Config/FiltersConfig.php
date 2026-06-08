<?php

namespace Wertelko\EasyadminMapperBundle\Config;

use Wertelko\EasyadminMapperBundle\Dto\FilterDto;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class FiltersConfig
{
    private array $filters = [];

    public static function new(): static
    {
        return new self();
    }

    /**
     * @param string|FilterInterface $filter
     * @param callable|null $callback fn(Entity $entity, string $value)
     *        null callback used for stub filter. In this case you might use \EasyAdmin\Dto\Filter\FilterDto as argument resolver
     * @return $this
     * @see \Wertelko\EasyadminMapperBundle\Dto\Filter\FilterDto
     */
    public function add(string|FilterInterface $filter, ?callable $callback = null): static
    {
        $callback ??= fn() => true;

        if (is_string($filter)) {
            $filter = TextFilter::new($filter);
        }

        $this->filters[] = new FilterDto($filter->getAsDto()->getProperty(), $callback, $filter);
        return $this;
    }

    /**
     * @return FilterDto[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}