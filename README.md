# EasyAdmin Mapper Bundle

A Symfony bundle that extends [EasyAdmin](https://github.com/EasyCorp/EasyAdminBundle) to work with **any data source** — files, APIs, arrays, or custom iterators — without requiring Doctrine ORM.

## Features

- 🗂️ **Non-Doctrine CRUD** — Use EasyAdmin's powerful interface for files, APIs, or any iterable data
- ⚡ **Batch Actions** — Full support for EasyAdmin batch actions with automatic DTO resolution
- 🎨 **EasyAdmin Fields** — Reuse any EasyAdmin field types (`IdField`, `TextField`, etc.)
- 🔧 **Custom Actions** — Create custom actions and batch operations
- 🎯 **Simple Integration** — Extend `AbstractMapperController` and implement `MapperControllerInterface`

## Requirements

- PHP 8.1 or higher
- Symfony 6.1 or higher
- EasyAdmin Bundle 4.x

## Installation

### Step 1: Install via Composer

```bash
composer require wertelko/easyadmin-mapper
```

### Step 2: Register the Bundle

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    Wertelko\EasyadminMapperBundle\EasyadminMapperBundle::class => ['all' => true],
];
```


## Quick Start

Create a controller that extends `AbstractMapperController` and implements `MapperControllerInterface`:

```php
<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wertelko\EasyadminMapperBundle\Contract\MapperControllerInterface;
use Wertelko\EasyadminMapperBundle\Controller\AbstractMapperController;

class TestController extends AbstractMapperController implements MapperControllerInterface
{
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX,
                Action::new('removeFile', 'Delete')
                    ->createAsBatchAction()
                    ->linkToRoute('admin_files')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('pathname');
        yield TextField::new('size')->formatValue(fn($value) => $this->formatBytes($value));
        yield DateTimeField::new('mtime', 'Modified');
    }

    #[Route('/admin/files', name: 'admin_files')]
    public function index(BatchActionDto $actionDto): Response
    {
        // Handle batch actions
        if ($actionDto->getName()) {
            return call_user_func([$this, $actionDto->getName()], $actionDto);
        }

        // Load your data from any source
        $finder = new Finder();
        $files = $finder->files()->in($this->getParameter('kernel.cache_dir'));

        // Render the EasyAdmin table
        return $this->renderTable($files, 'Cache files');
    }

    public function removeFile(BatchActionDto $actionDto): Response
    {
        foreach ($actionDto->getEntityIds() as $filename) {
            $path = $this->getParameter('kernel.cache_dir') . $filename;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $this->addFlash('success', sprintf('%d files deleted.', count($actionDto->getEntityIds())));

        return $this->redirectToRoute('admin_files');
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
```

## Usage Examples

### Working with API Data

```php
<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Wertelko\EasyadminMapperBundle\Contract\MapperControllerInterface;
use Wertelko\EasyadminMapperBundle\Controller\AbstractMapperController;

class UsersController extends AbstractMapperController implements MapperControllerInterface
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield TextField::new('name');
        yield EmailField::new('email');
    }

    #[Route('/admin/api-users', name: 'admin_api_users')]
    public function index(): Response
    {
        $response = $this->httpClient->request('GET', 'https://api.example.com/users');
        $users = $response->toArray();

        return $this->renderTable($users);
    }
}
```

### Working with Array Data

```php

public function configureFields(string $pageName): iterable
{
    yield IdField::new('id');
    yield TextField::new('level');
    yield DateTimeField::new('message');
}

#[Route('/admin/logs', name: 'admin_logs')]
public function logs(): Response
{
    $logs = [
        ['id' => 1, 'level' => 'ERROR', 'message' => 'Database connection failed'],
        ['id' => 2, 'level' => 'WARNING', 'message' => 'Disk space low'],
        ['id' => 3, 'level' => 'INFO', 'message' => 'Cache cleared'],
    ];

    return $this->renderTable($logs);
}
```

## How It Works

The bundle provides:

1. **`AbstractMapperController`** — Base controller with `renderTable()` method that converts any iterable/array into an EasyAdmin-compatible table
2. **`MapperControllerInterface`** — Marker interface that identifies your controllers as mapper controllers
3. **`BatchActionDtoValueResolver`** — Custom argument resolver that injects `BatchActionDto` into your controller actions for handling batch operations

## Configuration

The bundle automatically registers its services. No additional configuration is required.

## License

This bundle is released under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

If you encounter any issues or have questions, please [open an issue](https://github.com/wertelko/easyadmin-mapper/issues) on GitHub.
```
