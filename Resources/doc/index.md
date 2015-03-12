BazingaHateoasBundle
====================

This bundle integrates [Hateoas](http://github.com/willdurand/Hateoas) into
[Symfony2](http://symfony.com).

Installation
------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require willdurand/hateoas-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
        );
        
        // ...
    }
}
```

 > **Note:**
 > The bundle requires the [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle) to be
 > registered. If you haven't done that already, you should register it in the kernel aswell:
 >
 > ```php
 > // app/AppKernel.php
 > 
 > // ...
 > public function registerBundles()
 > {
 >     $bundles = array(
 >         // ...
 >         new JMS\SerializerBundle\JMSSerializerBundle(),
 >     );
 >
 >     // ...
 > }
 > ```

Usage
-----

### Mapping Objects

Refer to the [Hateoas documentation](http://github.com/willdurand/Hateoas) to
find out how to map your objects.

### Serializing objects

The BazingaHateoasBundle transparently hooks into the JMS serializer, there
are no special considerations:

````php
// My/Controller.php

class SomeController extends Controller
{
    public function resourceAction(Request $request)
    {
        $post = $repository->find('BlogBundle:post');
        $json = $this->container->get('serializer')->serialize($post, 'json');

        return new Response($json, 200, array('application/json'));
    }
}
````

Expression Language
-------------------

This bundle provides three extra functions to the expression language:

## `is_granted`

Allows you to exclude certain routes by checking whether the currently authenticated user
has certain permissions or not. For example:

```php
/**
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "post_delete",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted(['ROLE_ADMIN']))"
 *      )
 * )
 */
class Post
{
    // ...
}
```

If the authenticated user has the `ROLE_ADMIN` role the route will be exposed, otherwise
the route will be excluded.

## `parameter`

Allows you to fetch a parameter from the service container:

```
/**
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "post_delete",
 *          parameters = { "foo" = "expr(parameter('foo'))" }
 *      )
 * )
 */
class Post
{
    // ...
}
```

## `service`

Allows you to fetch a service from the service container.

Extending
---------

### RelationProviderResolver

A relation provider resolver is a class which provides a PHP callable. This
callable will provide relations (links) for a given object.

You can add new relation providers by implementing the 
`Hateoas\Configuration\Provider\Resolver\RelationProviderResolverInterface`
interface and adding a definition to the dependency injection configuration
with the tag `hateoas.relation_provider`:

````xml
<?xml version="1.0" ?>
<container ...>

    <!-- ... -->

    <services>
        <!-- ... -->

        <service id="acme_foo.hateoas.relation_provider_resolver.foobar" class="Acme\FooBundle\Hateoas\RelationProviderResolver\Foobar">
            <tag name="hateoas.relation_provider" />
        </service>
    </services>
</container>
````

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
