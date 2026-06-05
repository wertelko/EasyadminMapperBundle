<?php

namespace Wertelko\EasyadminMapperBundle\ArgumentResolver;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Wertelko\EasyadminMapperBundle\Contract\MapperControllerInterface;

class BatchActionDtoValueResolver implements ValueResolverInterface
{

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (BatchActionDto::class !== $argument->getType()) {
            return [];
        }

        [$controllerClass] = explode('::', $argument->getControllerName());

        if(!is_a($controllerClass, MapperControllerInterface::class, true)) {
            return [];
        }

        yield new BatchActionDto(
            $request->getPayload()->get(EA::BATCH_ACTION_NAME, ''),
            $request->getPayload()->all(EA::BATCH_ACTION_ENTITY_IDS),
            $request->getPayload()->get(EA::ENTITY_FQCN, ''),
            '',
            $request->getPayload()->get(EA::BATCH_ACTION_CSRF_TOKEN, ''),
            false
        );
    }
}