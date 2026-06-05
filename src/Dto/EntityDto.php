<?php

namespace Wertelko\EasyadminMapperBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Collection\ActionCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EntityDto
{
    private PropertyAccessorInterface $accessor;
    private ?ActionCollection $actions = null;
    private ?FieldCollection $fields = null;
    private $fqcn;
    private $instance;
    private bool $isAccessible = true;
    private $primaryKeyName;
    private mixed $primaryKeyValue = null;
    private readonly array $propertiesNames;
    private array $propertiesValues = [];

    /**
     * @param object $entityInstance
     * @param array|null $propertiesNames
     * @param string|null $identifierName
     */
    public function __construct(object $entityInstance, ?array $propertiesNames = [], ?string $identifierName = 'id')
    {
        $this->fqcn = get_class($entityInstance);
        $this->instance = $entityInstance;
        $this->primaryKeyName = $identifierName;
        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
        $this->propertiesNames = $propertiesNames;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function getActions(): ?ActionCollection
    {
        return $this->actions;
    }

    public function setActions(ActionCollection $actions): void
    {
        $this->actions = $actions;
    }

    /**
     * Returns the names of all properties defined in the entity, no matter
     * if they are used or not in the application.
     */
    public function getAllPropertyNames(): array
    {
        return $this->propertiesNames;
    }

    public function getFields(): ?FieldCollection
    {
        return $this->fields;
    }

    public function setFields(FieldCollection $fields): void
    {
        $this->fields = $fields;
    }

    public function getFqcn()
    {
        return $this->fqcn;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function getName(): string
    {
        return basename(str_replace('\\', '/', $this->getFqcn()));
    }

    public function getPrimaryKeyName(): string
    {
        return $this->primaryKeyName;
    }

    public function getPrimaryKeyValue(): mixed
    {
        return $this->getValue($this->primaryKeyName);
    }

    public function getPrimaryKeyValueAsString(): string
    {
        return (string)$this->getPrimaryKeyValue();
    }

    public function getValue(string $propertyName): mixed
    {
        if (null === $this->instance) {
            return null;
        }

        if (\array_key_exists($propertyName, $this->propertiesValues)) {
            return $this->propertiesValues[$propertyName];
        }

        $this->propertiesValues[$propertyName] = $this->accessor->getValue($this->instance, $propertyName);

        return $this->propertiesValues[$propertyName];
    }

    public function hasProperty(string $name): bool
    {
        return \in_array($name, $this->propertiesNames);
    }

    public function newWithInstance(object $newEntityInstance): self
    {
        if (null !== $this->instance && !$newEntityInstance instanceof $this->fqcn) {
            throw new \InvalidArgumentException(sprintf('The new entity instance must be of the same type as the previous instance (original instance: "%s", new instance: "%s").', $this->fqcn, \get_class($newEntityInstance)));
        }

        return new self($newEntityInstance, $this->propertiesNames, $this->primaryKeyName);
    }

    public function toString(): string
    {
        if (null === $this->instance) {
            return '';
        }

        if (method_exists($this->instance, '__toString')) {
            return (string)$this->instance;
        }

        return sprintf('%s #%s', $this->getName(), substr($this->getPrimaryKeyValueAsString(), 0, 16));
    }
}
