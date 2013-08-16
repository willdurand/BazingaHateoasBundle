BazingaHateoasBundle
====================

This bundle integrates [Hateoas](http://github.com/willdurand/Hateoas) into
[Symfony2](http://symfony.com).

Usage
-----

Basically, this bundle does not need anything. You should look at the [Hateoas
documentation](http://github.com/willdurand/Hateoas) for more information.


Installation
------------

Require [`willdurand/hateoas-bundle`](https://packagist.org/packages/willdurand/hateoas-bundle)
to your `composer.json` file:


``` json
{
    "require": {
        "willdurand/hateoas-bundle": "@stable"
    }
}
```

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
    );
}
```


Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

    $ composer install --dev

Run it using PHPUnit:

    $ phpunit
