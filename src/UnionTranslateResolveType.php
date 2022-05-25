<?php

namespace JBernavaPrah\LighthouseTranslation;

use GraphQL\Error\Error;
use GraphQL\Executor\Values;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class UnionTranslateResolveType
{

    const TRANSLATION_TYPE_NAME = "Translation";
    const RAW_TRANSLATION_TYPE_NAME = "RawTranslation";

    protected string $resolveType = self::RAW_TRANSLATION_TYPE_NAME;

    public function __construct(protected TypeRegistry $typeRegistry)
    {
    }


    /**
     * Decide which GraphQL type a resolved value has.
     *
     * @param mixed $rootValue The value that was resolved by the field. Usually an Eloquent model.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
     * @return \GraphQL\Type\Definition\Type
     * @throws Error
     * @throws \Nuwave\Lighthouse\Exceptions\DefinitionException
     */
    public function __invoke($rootValue, GraphQLContext $context, ResolveInfo $resolveInfo): Type
    {

        if ($rootValue instanceof Translate) {
            return $this->typeRegistry->get(self::TRANSLATION_TYPE_NAME);
        }

        return $this->typeRegistry->get(self::RAW_TRANSLATION_TYPE_NAME);
    }


}
