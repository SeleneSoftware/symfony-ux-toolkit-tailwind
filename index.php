<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\UX\TwigComponent\TwigComponentBundle;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new TwigComponentBundle(),
        ];
    }

    protected function buildContainer(): ContainerBuilder
    {
        $container = parent::buildContainer();

        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                if ($container->hasDefinition('twig')) {
                    $container->getDefinition('twig')->setPublic(true);
                }
            }
        });

        return $container;
    }

    public function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'test',
            'test' => true,
            'router' => ['utf8' => true],
        ]);

        $twigPaths = [__DIR__];
        foreach (glob(__DIR__.'/*/templates', GLOB_ONLYDIR) as $dir) {
            $twigPaths[] = $dir;
        }

        $container->extension('twig', [
            'paths' => $twigPaths,
            'strict_variables' => true,
        ]);

        $container->extension('twig_component', [
            'defaults' => [],
            'anonymous_template_directory' => 'components',
        ]);
    }

    public function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->add('test', '/')->controller('kernel::renderTest');
    }

    public function renderTest(): Response
    {
        $content = $this->container->get('twig')->render('test.html.twig');

        return new Response($content);
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/sbolch-test-ux/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/sbolch-test-ux/log';
    }
}

$kernel = new TestKernel('test', true);
$kernel->boot();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
