<?php

declare(strict_types=1);

namespace Bazinga\Bundle\HateoasBundle\Tests\Fixtures;

use Hateoas\UrlGenerator\UrlGeneratorInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    public function generate(string $name, array $parameters, $absolute = false): string
    {
    }
}
