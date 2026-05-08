# Component kit based on Bootstrap 5.3 for Symfony Toolkit

## Requirements

- [Boostrap 5.3](https://getbootstrap.com/docs/5.3/getting-started/download/)
- [Symfony UX Toolkit](https://ux.symfony.com/toolkit)

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

> Note: The package is in progress, Bootstrap components might still be missing and bugs might happen.
