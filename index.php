<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\UX\Icons\Iconify;
use Symfony\UX\Icons\IconRenderer;
use Symfony\UX\Icons\Registry\CacheIconRegistry;
use Symfony\UX\Icons\Registry\IconifyOnDemandRegistry;
use Symfony\UX\Icons\Twig\UXIconRuntime;
use Symfony\UX\TwigComponent\ComponentFactory;
use Symfony\UX\TwigComponent\ComponentProperties;
use Symfony\UX\TwigComponent\ComponentRenderer;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Symfony\UX\TwigComponent\ComponentStack;
use Symfony\UX\TwigComponent\ComponentTemplateFinder;
use Symfony\UX\TwigComponent\Twig\ComponentExtension;
use Symfony\UX\TwigComponent\Twig\ComponentLexer;
use Symfony\UX\TwigComponent\Twig\ComponentRuntime;
use Twig\Environment;
use Twig\Extra\Html\HtmlExtension;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

// 1. Collect all component template directories
$componentTemplateDirs = [];
foreach (glob(__DIR__.'/*/templates', GLOB_ONLYDIR) as $dir) {
    $componentTemplateDirs[] = realpath($dir);
}

// 2. Twig loader — project root first + all component templates directories
$loader = new FilesystemLoader();
$loader->addPath(__DIR__);
foreach ($componentTemplateDirs as $dir) {
    $loader->addPath($dir);
}

// 3. Twig environment
$twig = new Environment($loader, [
    'cache' => false,
    'autoescape' => 'html',
    'strict_variables' => false,
]);

// 4. Component lexer (converts <twig:Component> into Twig syntax)
$twig->setLexer(new ComponentLexer($twig));

// 5. Twig extensions
$twig->addExtension(new HtmlExtension());      // html_cva()
$twig->addExtension(new ComponentExtension()); // {% props %}, {% component %}, {{ component() }}

// 6. Mark ComponentAttributes as safe for HTML (prevents double-escaping of {{ attributes }})
$twig->getRuntime(\Twig\Runtime\EscaperRuntime::class)->addSafeClass(ComponentAttributes::class, ['html']);

// 7. Icon registry (fetches real icons from Iconify CDN)
@mkdir(__DIR__.'/_cache/icons', 0777, true);
$cache = new FilesystemAdapter('ux_icons', 86400, __DIR__.'/_cache/icons');

$iconify = new Iconify($cache, httpClient: HttpClient::create());
$iconRegistry = new CacheIconRegistry(
    new IconifyOnDemandRegistry($iconify),
    $cache,
);
$iconRenderer = new IconRenderer($iconRegistry);
$iconRuntime = new UXIconRuntime($iconRenderer, true);

// 8. Component infrastructure
$eventDispatcher = new EventDispatcher();
$propertyAccessor = PropertyAccess::createPropertyAccessor();
$componentStack = new ComponentStack();
$componentTemplateFinder = new ComponentTemplateFinder($loader, 'components');
$componentFactory = new ComponentFactory(
    $componentTemplateFinder,
    new ServiceLocator([]),
    $propertyAccessor,
    $eventDispatcher,
    [],
    [],
    $twig,
);
$componentProperties = new ComponentProperties($propertyAccessor);
$componentRenderer = new ComponentRenderer(
    $twig,
    $eventDispatcher,
    $componentFactory,
    $componentProperties,
    $componentStack,
);
$componentRuntime = new ComponentRuntime(
    $componentRenderer,
    new ServiceLocator([
        'ux:icon' => static fn() => $iconRuntime,
    ]),
);

// 9. Register runtime loaders
$twig->addRuntimeLoader(new class([
    ComponentRuntime::class => $componentRuntime,
    UXIconRuntime::class => $iconRuntime,
]) implements RuntimeLoaderInterface {
    private array $runtimes;

    public function __construct(array $runtimes)
    {
        $this->runtimes = $runtimes;
    }

    public function load(string $class): ?object
    {
        return $this->runtimes[$class] ?? null;
    }
});

// 10. Render
echo $twig->render('index.html.twig');
