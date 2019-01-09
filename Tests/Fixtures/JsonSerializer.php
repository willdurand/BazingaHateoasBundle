<?php

declare(strict_types=1);

namespace Bazinga\Bundle\HateoasBundle\Tests\Fixtures;

use Hateoas\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

class JsonSerializer implements SerializerInterface
{
    public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context): void
    {
    }

    public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context): void
    {
    }
}
