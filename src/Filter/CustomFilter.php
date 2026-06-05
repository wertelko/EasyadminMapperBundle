<?php

namespace Wertelko\EasyadminMapperBundle\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomFilter extends AbstractCustomFilter implements FilterInterface
{

    use FilterTrait;

    public static function new(string $propertyName, ?string $label = null)
    {
        return (new self())
            ->setLabel($label)
            ->setProperty($propertyName)
            ->setFormType(TextType::class);
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        if ($this->existsApply()) {
            ($this->getApply())($queryBuilder, $filterDataDto, $fieldDto, $entityDto);
        }
    }
}