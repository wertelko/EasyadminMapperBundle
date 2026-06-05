<?php

namespace Wertelko\EasyadminContentBundle\Config;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

class EntityConfig
{
    private string $fqcn;
    private ?string $propertyIdName;
    private ?array $propertyNames;

    public static function new(string $fqcn)
    {
        return (new self())->setFqsn($fqcn);
    }

    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    public function setFqcn(string $fqcn): static
    {
        $this->fqcn = $fqcn;
        return $this;
    }

    public function getPropertyIdName(): ?string
    {
        return $this->propertyIdName;
    }

    public function setPropertyIdName(?string $propertyIdName): static
    {
        $this->propertyIdName = $propertyIdName;
        return $this;
    }

    public function getPropertyNames(): array
    {
        if (!isset($this->propertyNames)) {
            $this->propertyNames = (new ReflectionExtractor())->getProperties($this->fqcn);
        }
        return $this->propertyNames;
    }

    public function setPropertyNames(?array $propertyNames): static
    {
        $this->propertyNames = $propertyNames;
        return $this;
    }

    public function setFqsn(string $fqcn)
    {
        $this->fqcn = $fqcn;
        return $this;
    }
}