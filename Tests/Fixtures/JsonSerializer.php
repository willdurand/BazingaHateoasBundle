<?php

namespace Bazinga\Bundle\HateoasBundle\Tests\Fixtures;

use Hateoas\Serializer\JsonSerializerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

class JsonSerializer implements JsonSerializerInterface
{
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
    }

    public function serializeEmbeddeds(array $embeddeds, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
    }
}
