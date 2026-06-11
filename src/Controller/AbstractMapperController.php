<?php

namespace Wertelko\EasyadminMapperBundle\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Wertelko\EasyadminMapperBundle\Collection\EntityCollection;
use Wertelko\EasyadminMapperBundle\Config\EntityConfig;
use Wertelko\EasyadminMapperBundle\Config\FiltersConfig;
use Wertelko\EasyadminMapperBundle\Contract\MapperControllerInterface;
use Wertelko\EasyadminMapperBundle\Dto\EntityDto;
use Wertelko\EasyadminMapperBundle\Dto\FilterDto;
use Wertelko\EasyadminMapperBundle\Factory\ActionFactory;
use Wertelko\EasyadminMapperBundle\Factory\EntityFactory;
use Wertelko\EasyadminMapperBundle\Factory\FilterFactory;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractMapperController extends AbstractController implements MapperControllerInterface
{
    public static function getSubscribedServices(): array
    {
        return [
            RequestStack::class,
            EntityFactory::class,
            ActionFactory::class,
            FilterFactory::class,
            AdminUrlGenerator::class,
            ...parent::getSubscribedServices()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }

    public function configureEntity(EntityConfig $config, iterable $fields = []): EntityConfig
    {
        $propertyNames = [];
        $id = null;

        foreach ($fields as $field) {
            if (is_string($field)) {
                $propertyNames[] = $field;
                continue;
            }

            if ($field instanceof FieldDto) {
                if ($field->getFieldFqcn() == IdField::class) {
                    $id = $field->getProperty();
                }

                $propertyNames[] = $field->getProperty();
            }
        }

        return $config->setPropertyNames($propertyNames)->setPropertyIdName($id);
    }

    public function configureFilters(FiltersConfig $filters): FiltersConfig
    {
        return $filters;
    }


    /**
     * Prepare data for table
     *
     * @param iterable $entities
     * @param string $title
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function prepareIndex(iterable $entities, string $title = 'Title'): array
    {
        $request = $this->container->get(RequestStack::class)->getCurrentRequest();
        $pageName = $request->query->get(EA::CRUD_ACTION, Crud::PAGE_INDEX);
        $fields = FieldCollection::new($this->configureFields($pageName));
        $actions = $this->configureActions(Actions::new())->getAsDto($pageName);
        $filtersQuery = $request->query->all('filters');
        $filters = $this->container->get(FilterFactory::class)->create($this->configureFilters(FiltersConfig::new())->getFilters(), $filtersQuery);

        $entityDtos = [];

        foreach ($entities as $entity) {
            if (!isset($config)) {
                if (is_array($entity)) {
                    $entity = (object) $entity;
                }
                if (!is_object($entity)) {
                    continue;
                }

                $config = $this->configureEntity(EntityConfig::new(get_class($entity)), $fields);
            }

            if ($this->isFilterIsPassed($filters, $filtersQuery, $entity)) {
                if (is_array($entity)) {
                    $entity = (object) $entity;
                }
                $entityDtos[] = $this->makeEntityDto($entity, $config);
            }

        }

        $collection = EntityCollection::new($entityDtos);
        $this->container->get(EntityFactory::class)->processFieldsForAll($collection, $fields);
        $actions = $this->container->get(EntityFactory::class)->processActionsForAll(
            $collection, $actions
        );

        return [
            'title' => $title,
            'filters' => array_map(fn(FilterDto $f) => $f->getFilterType()->getAsDto(), $filters),
            'global_actions' => $actions->getGlobalActions(),
            'batch_actions' => $actions->getBatchActions(),
            'entities' => $entityDtos,
        ];
    }

    /**
     * @param iterable $entities
     * @param string|null $title
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function renderTable(iterable $entities, ?string $title = 'Title'): Response
    {
        return $this->render(
            '@EasyadminMapper/crud/index.html.twig',
            $this->prepareIndex($entities, $title)
        );
    }

    /**
     * @param $filters FilterDto[]
     * @param $filtersQuery array<string, string>
     * @param $entity mixed Entity or Dto
     * @return bool
     */
    private function isFilterIsPassed(array $filters, array $filtersQuery, mixed $entity): bool
    {
        if (empty($filtersQuery)) return true;

        foreach ($filters as $filter) {
            if (!isset($filtersQuery[$filter->getName()])) continue;
            if (!($filter->getCallback())($entity, $filtersQuery[$filter->getName()])) {
                return false;
            }
        }

        return true;
    }

    private function makeEntityDto(object $dto, EntityConfig $config): EntityDto
    {
        return new EntityDto($dto, $config->getPropertyNames(), $config->getPropertyIdName());
    }
}