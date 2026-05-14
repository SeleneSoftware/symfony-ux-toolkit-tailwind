# Component kit based on Bootstrap 5.3 for Symfony Toolkit

## Requirements

- [Symfony UX Toolkit](https://ux.symfony.com/toolkit)
- [Installation](INSTALL.md)

## Component installation

### Install the components you need...

eg. for Button:

```sh
php bin/console ux:install button --kit https://github.com/sbolch/symfony-ux-toolkit-bootstrap
```

... and find them in your templates/components folder!

### Usage

```twig
{# Freshly installed components are ready to use! #}
<twig:Button variant="primary" outline="true" href="https://symfony.com" target="_blank">
    Visit symfony.com
</twig:Button>
```

For component usage examples, see [test.html.twig](test.html.twig).

> Note: The package is in progress, Bootstrap components might still be missing and bugs might happen.
