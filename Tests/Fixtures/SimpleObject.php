<?php

declare(strict_types=1);

namespace Bazinga\Bundle\HateoasBundle\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

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
 * @Hateoas\Relation(
 *     "e1",
 *     embedded=@Hateoas\Embedded(
 *         "expr(1)",
 *         type="string"
 *    )
 * )
 * @Hateoas\Relation(
 *     "e2",
 *     embedded=@Hateoas\Embedded(
 *         "expr(2)",
 *         type="float",
 *          exclusion=@Hateoas\Exclusion(excludeIf="expr(false)")
 *    )
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
