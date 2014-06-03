<?php

namespace Bazinga\Bundle\HateoasBundle\Tests\Fixtures;

use Hateoas\UrlGenerator\UrlGeneratorInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    public function generate($name, array $parameters, $absolute = false)
    {

    }
}
