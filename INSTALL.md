# Getting started

This kit provides ready-to-use and fully-customizable UI Twig components based on [Bootstrap 5.3](https://getbootstrap.com/docs/5.3/) components's **design**.

Please note that not every Bootstrap component is available in this kit, but we are working on it!

## Requirements

This kit requires Bootstrap 5.3 to work:

- If you use Symfony AssetMapper, you can install Bootstrap with the [AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html),
- If you use Webpack Encore, you can follow the [Bootstrap installation guide for Symfony](https://getbootstrap.com/docs/5.3/getting-started/webpack/)

## Installation

Modify the file `assets/styles/app.css` with the following content:

```css
@import 'bootstrap/dist/css/bootstrap.min.css';
```

And in your `assets/app.js`:

```javascript
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
```

If you prefer using CDN, add the following to your base template (e.g., `templates/base.html.twig`):

```twig
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```
