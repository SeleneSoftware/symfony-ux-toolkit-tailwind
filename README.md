# Component kit based on Bootstrap 5.3 for Symfony Toolkit

## Requirements

- [Symfony UX Toolkit](https://ux.symfony.com/toolkit)
- [Installation](INSTALL.md)

## Component installation

### Install the components you need...

eg. for Button:

```sh
php bin/console ux:install button --kit https://github.com/SeleneSoftware/symfony-ux-toolkit-tailwind
```

... and find them in your templates/components folder!

### Usage

```twig
{# Freshly installed components are ready to use! #}
<twig:Button as="a" variant="outline-primary" href="https://symfony.com" target="_blank">
    Visit symfony.com
</twig:Button>
```

For component usage examples, see every components' examples folder.

> Note: The package is in progress, HyperUI components might still be missing and bugs might happen.
