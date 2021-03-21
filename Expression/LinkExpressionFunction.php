<?php

declare(strict_types=1);

namespace Bazinga\Bundle\HateoasBundle\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class LinkExpressionFunction implements ExpressionFunctionProviderInterface
{
    /**
     * @return ExpressionFunction[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction('link', static function ($object, $rel, $absolute = false) {
                    return sprintf('$container->get(\'hateoas.helper.link\')->getLinkHref(%s, %s, %s)', $object, $rel, $absolute);
            }, static function ($context, $object, $rel, $absolute = false) {
                return $context['container']->get('hateoas.helper.link')->getLinkHref($object, $rel, $absolute);
            }),
        ];
    }
}
