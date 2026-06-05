<?php

namespace Wertelko\EasyadminContentBundle\Factory;

use Wertelko\EasyadminContentBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;

class FieldFactory
{
    public function __construct()
    {
    }

    public function processFields(EntityDto $entityDto, FieldCollection $fields): void
    {
        /** @var FieldDto $fieldDto */
        foreach ($fields as $fieldDto) {

            if (!$entityDto->hasProperty($fieldDto->getProperty())) {
                continue;
            }

            if (!$fieldDto->isVirtual()) {
                $fieldDto->setValue($entityDto->getValue($fieldDto->getProperty()));
            }

            /*if (\is_callable($fieldDto->getFormattedValue())) {
                $fieldDto->setFormattedValue($fieldDto->getFormattedValue()($entityDto->getInstance()));
            } else {
                $fieldDto->setFormattedValue($fieldDto->getValue());
            }*/

            if (is_callable($fieldDto->getFormatValueCallable())) {
                $fieldDto->setFormattedValue($fieldDto->getFormatValueCallable()($fieldDto->getValue(), $entityDto->getInstance()));
            } else {
                $fieldDto->setFormattedValue($fieldDto->getValue());
            }

            if (null === $fieldDto->getLabel()) {
                $fieldDto->setLabel(ucfirst($fieldDto->getProperty()));
            }

            if (null === $fieldDto->getTemplatePath()) {
                $fieldDto->setTemplatePath('admin/crud/field/text.html.twig');
            }
        }

        $entityDto->setFields($fields);
    }
}