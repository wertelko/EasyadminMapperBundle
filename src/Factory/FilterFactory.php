<?php

namespace Wertelko\EasyadminMapperBundle\Factory;

use Wertelko\EasyadminMapperBundle\Dto\FilterDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class FilterFactory
{
    private const TEMPLATES = [
        TextFilter::class => '@EasyadminMapper/crud/include/filter/text.html.twig',
        BooleanFilter::class => '@EasyadminMapper/crud/include/filter/checkbox.html.twig',
        ArrayFilter::class => '@EasyadminMapper/crud/include/filter/select.html.twig',
        DateTimeFilter::class => '@EasyadminMapper/crud/include/filter/datetime.html.twig',
    ];

    /**
     * @param FilterDto[] $filters
     * @return FilterDto[]
     */
    public function create(array $filters, ?array $appliedFilters = []): array
    {
        foreach ($filters as $filter) {
            $this->configureFilter($filter, $appliedFilters);
        }

        return $filters;
    }

    private function configureFilter(FilterDto $filter, ?array $appliedFilters = []): void
    {
        $filterDto = $filter->getFilterType()->getAsDto();

        $value = $appliedFilters[$filterDto->getProperty()] ?? null;

        if (null === $filterDto->getLabel()) {
            $filterDto->setLabel(ucfirst($filterDto->getProperty()));
        }

        $filterDto->setFormTypeOption('template', self::TEMPLATES[$filterDto->getFqcn()]);

        $filterDto->setFormTypeOption('value', $value);
    }
}