<?php

namespace Bazinga\Bundle\HateoasBundle\Hateoas\Expression;

use Hateoas\Expression\ExpressionEvaluator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LazyFunctionExpressionEvaluator extends ExpressionEvaluator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $functionsIds = array();

    public function __construct(
        ExpressionLanguage $expressionLanguage,
        array $context = array(),
        ContainerInterface $container
    ) {
        parent::__construct($expressionLanguage, $context);

        $this->container = $container;
    }

    public function evaluate($expression, $data)
    {
        if (!empty($this->functionsIds)) {
            $this->registerFunctions();
        }

        return parent::evaluate($expression, $data);
    }

    public function evaluateArray(array $array, $data)
    {
        if (!empty($this->functionsIds)) {
            $this->registerFunctions();
        }

        return parent::evaluateArray($array, $data);
    }

    public function registerFunctionId($serviceId)
    {
        $this->functionsIds[] = $serviceId;
    }

    private function registerFunctions()
    {
        foreach ($this->functionsIds as $serviceId) {
            $this->registerFunction($this->container->get($serviceId));
        }
        $this->functionsIds = array();
    }
}
