<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\UX\Icons\UXIconsBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Twig\Environment;
use Twig\Extra\Html\HtmlExtension;

$kernel = new class('dev', true) extends Kernel {
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new TwigComponentBundle();
        yield new UXIconsBundle();
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return __DIR__.'/_cache/'.$this->environment;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $twigPaths = [__DIR__ => ''];
        foreach (glob(__DIR__.'/*/templates', GLOB_ONLYDIR) as $dir) {
            $twigPaths[realpath($dir)] = '';
        }

        $container->extension('framework', [
            'secret' => 'symfony-ux-toolkit-bootstrap',
            'http_client' => true,
            'property_access' => true,
        ]);

        $container->extension('twig', [
            'cache' => false,
            'strict_variables' => false,
            'paths' => $twigPaths,
        ]);

        $container->extension('twig_component', [
            'defaults' => [],
            'anonymous_template_directory' => 'components',
        ]);

        $container->extension('ux_icons', []);

        $container->services()
            ->alias(Environment::class, 'twig')
            ->public()
            ->set(HtmlExtension::class)
            ->tag('twig.extension')
        ;
    }
};

$kernel->boot();

echo $kernel->getContainer()->get(Environment::class)->render('index.html.twig');
