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
into your `composer.json` file:


``` json
{
    "require": {
        "willdurand/hateoas-bundle": "@stable"
    }
}
```

**Protip:** you should browse the
[`willdurand/hateoas-bundle`](https://packagist.org/packages/willdurand/hateoas-bundle)
page to choose a stable version to use, avoid the `@stable` meta constraint.

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle(),
        new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
    );
}
```


Reference Configuration
-----------------------

``` yaml
# app/config/config*.yml

bazinga_hateoas:
    metadata:
        cache:                file
        file_cache:
            dir:              %kernel.cache_dir%/hateoas
```


Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

    $ composer install --dev

Run it using PHPUnit:

    $ phpunit
