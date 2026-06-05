<?php

namespace Wertelko\EasyadminMapperBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;

class FilterDto
{
    public function __construct(
        private readonly string          $name,
        /**
         * @var callable
         */
        private                          $callback,
        private readonly FilterInterface $filterType,
    )
    {
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }

    public function getFilterType(): FilterInterface
    {
        return $this->filterType;
    }

    public function getName(): string
    {
        return $this->name;
    }
}