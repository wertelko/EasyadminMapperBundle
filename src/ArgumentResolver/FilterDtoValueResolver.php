<?php

namespace Wertelko\EasyadminMapperBundle\ArgumentResolver;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Wertelko\EasyadminMapperBundle\Contract\MapperControllerInterface;
use Wertelko\EasyadminMapperBundle\Dto\Filter\FilterDto;

class FilterDtoValueResolver implements ValueResolverInterface
{

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (FilterDto::class !== $argument->getType()) {
            return [];
        }

        yield new FilterDto($request->query->all('filters'));
    }
}