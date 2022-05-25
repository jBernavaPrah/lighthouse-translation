<?php

namespace JBernavaPrah\LighthouseTranslation;

use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Language\AST\ObjectTypeExtensionNode;
use GraphQL\Language\Parser;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\ClientDirectives\ClientDirective;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use phpDocumentor\Reflection\Types\This;

class Localize
{

    public function resolve(\Closure $closure, string $language): mixed
    {
        return Arr::first($closure(), fn(Translate $array) => $array->lang === $language);
    }







}