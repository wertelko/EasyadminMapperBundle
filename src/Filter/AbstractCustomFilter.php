<?php

namespace Wertelko\EasyadminMapperBundle\Filter;

class AbstractCustomFilter
{
    /**
     * @var callable
     */
    private $apply;

    public function existsApply(): bool
    {
        return isset($this->apply);
    }

    public function getApply(): callable
    {
        return $this->apply;
    }

    /**
     * @param callable $apply fn(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto)
     * @return static
     */
    public function setApply(callable $apply): static
    {
        $this->apply = $apply;
        return $this;
    }
}