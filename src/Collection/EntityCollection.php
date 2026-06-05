<?php

namespace Wertelko\EasyadminMapperBundle\Collection;

use Wertelko\EasyadminMapperBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Collection\CollectionInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EntityCollection implements CollectionInterface
{
    /**
     * @param EntityDto[] $entities
     */
    private function __construct(private array $entities)
    {
    }

    /**
     * @param EntityDto[] $entities
     */
    public static function new(array $entities): self
    {
        return new self($entities);
    }

    public function count(): int
    {
        return \count($this->entities);
    }

    public function get(string $entityId): ?EntityDto
    {
        return $this->entities[$entityId] ?? null;
    }

    /**
     * @return \ArrayIterator<EntityDto>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->entities);
    }

    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->entities);
    }

    public function offsetGet(mixed $offset): EntityDto
    {
        return $this->entities[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->entities[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->entities[$offset]);
    }

    public function set(EntityDto $newOrUpdatedEntity): void
    {
        $this->entities[$newOrUpdatedEntity->getPrimaryKeyValueAsString()] = $newOrUpdatedEntity;
    }
}
