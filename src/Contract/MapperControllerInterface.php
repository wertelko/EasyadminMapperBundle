<?php

namespace Wertelko\EasyadminMapperBundle\Contract;

use Wertelko\EasyadminMapperBundle\Config\EntityConfig;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

interface MapperControllerInterface
{
    public function configureActions(Actions $actions): Actions;

    public function configureEntity(EntityConfig $config, iterable $fields = []): EntityConfig;

    public function configureFields(string $pageName): iterable;
}