<?php

namespace JBernavaPrah\LighthouseTranslation;

use Closure;
use GraphQL\Error\Error;
use GraphQL\Language\AST\NonNullTypeNode;
use GraphQL\Language\AST\TypeNode;
use GraphQL\Type\Definition\Directive;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Collection;
use JBernavaPrah\LighthouseTranslation\Services\TranslatorService;
use JBernavaPrah\LighthouseTranslation\UnionTranslateResolveType;
use Nuwave\Lighthouse\ClientDirectives\ClientDirective;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\RootType;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class LocalizableDirective extends BaseDirective implements FieldMiddleware
{
    public const LOCALIZE_DIRECTIVE_NAME = 'localize';
    const TRANSLATE = "Translate";
    public static array $localized = [];

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Do not use this directive directly, it is automatically added to the schema
when using the localize extension.
"""
directive @localizable on FIELD_DEFINITION
GRAPHQL;
    }

    public function __construct(protected UnionTranslateResolveType $unionTranslateResolveType,
                                protected Localize                  $localize)
    {

    }

    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {

        $previousResolver = $fieldValue->getResolver();
        self::$localized = [];

        $fieldValue->setResolver(function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($fieldValue, $previousResolver) {

            $wrappedResolver = function () use ($previousResolver, $root, $args, $context, $resolveInfo) {
                return $previousResolver($root, $args, $context, $resolveInfo);
            };

            $localizes = (new ClientDirective(self::LOCALIZE_DIRECTIVE_NAME))->forField($resolveInfo);
            $path = implode('.', $resolveInfo->path);

            if ($this->anyFieldHasLocalize($localizes)) {

                self::$localized[$path] = Collection::wrap($localizes)
                    ->reject(fn($array) => !isset($array['lang']))
                    ->map(fn($array) => $array["lang"])
                    ->last();
            }

            if (ASTHelper::getUnderlyingTypeName($fieldValue->getField()) == self::TRANSLATE
                and $locale = $this->shouldLocalizeIntoLocale($path)) {
                return $this->localize->resolve($wrappedResolver, $locale);
            }

            return $wrappedResolver();
        });

        return $next($fieldValue);
    }

    /**
     * @param array<array<string, mixed>|null> $localizes
     */
    protected function anyFieldHasLocalize(array $localizes): bool
    {
        foreach ($localizes as $localize) {
            if (null !== $localize) {
                return true;
            }
        }

        return false;
    }

    protected function shouldLocalizeIntoLocale($path): ?string
    {

        $keys = explode('.', $path);

        foreach ($keys as $_) {
            foreach (self::$localized as $key => $locale) {
                if ($key === implode(".", $keys)) {
                    return $locale;
                }
            }
            array_pop($keys);
        }
        return null;

    }

}
