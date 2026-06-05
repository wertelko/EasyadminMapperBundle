<?php

namespace Wertelko\EasyadminMapperBundle;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Wertelko\EasyadminMapperBundle\DependencyInjection\EasyadminMapperExtension;

class EasyadminMapperBundle extends AbstractBundle
{
    public function getContainerExtension(): EasyadminMapperExtension
    {
        return new EasyadminMapperExtension();
    }
}