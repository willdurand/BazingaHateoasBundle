<?php

namespace Bazinga\Bundle\HateoasBundle\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     "all",
 *     href = "http://somewhere/simple-objects",
 *     attributes = { "foo" = "expr(parameter('foo'))" }
 * )
 * @Hateoas\Relation(
 *     "all_2",
 *     href = "expr(link(object, 'all'))"
 * )
 */
class SimpleObject
{
    /**
     * @Type("string")
     * @SerializedName("name")
     */
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
