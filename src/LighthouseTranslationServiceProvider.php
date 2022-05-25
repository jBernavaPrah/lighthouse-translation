<?php
declare(strict_types=1);

namespace JBernavaPrah\LighthouseTranslation;

use GraphQL\Language\AST\DirectiveDefinitionNode;
use GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Language\AST\UnionTypeDefinitionNode;
use GraphQL\Language\Parser;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use JBernavaPrah\LighthouseTranslation\Services\TranslatorService;
use Nuwave\Lighthouse\Events\ManipulateAST;
use Nuwave\Lighthouse\Events\RegisterDirectiveNamespaces;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\RootType;

class LighthouseTranslationServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function register()
    {


        $this->app->singleton(TranslatorService::class);
        $this->app->singleton(UnionTranslateResolveType::class);
        $this->app->singleton(Localize::class);
        // register the resolver to lang
        // register the resolver to enable-disable

    }


    /**
     * Bootstrap any application services.
     *
     * @param Dispatcher $dispatcher
     * @return void
     */
    public function boot(Dispatcher $dispatcher): void
    {

        $dispatcher->listen(
            RegisterDirectiveNamespaces::class,
            static function (): string {
                return __NAMESPACE__;
            }
        );

        $dispatcher->listen(
            ManipulateAST::class,
            function (ManipulateAST $manipulateAST) {

                $manipulateAST->documentAST->setTypeDefinition(self::translationType());
                $manipulateAST->documentAST->setTypeDefinition(self::rawTranslationType());
                $manipulateAST->documentAST->setTypeDefinition(self::unionTranslateType());
                $manipulateAST->documentAST->setTypeDefinition(self::inputTranslateType());
                $manipulateAST->documentAST->setDirectiveDefinition(self::localizeClientDirectiveType());


                ASTHelper::attachDirectiveToObjectTypeFields(
                    $manipulateAST->documentAST,
                    Parser::constDirective(/** @lang GraphQL */ '@localizable')
                );

            }
        );


    }

    static function inputTranslateType(): InputObjectTypeDefinitionNode
    {
        return Parser::inputObjectTypeDefinition(/** @lang GraphQL */ <<<GRAPHQL

input TranslateInput {
    text: String!
    lang: String! @rules(apply: ["size:2"])
}
GRAPHQL
        );
    }

    static function unionTranslateType(): UnionTypeDefinitionNode
    {
        return Parser::unionTypeDefinition(/** @lang GraphQL */ <<<GRAPHQL
"""
The field can be translated (from `localize` directive).

If no localize is provided, the field will return the RawTranslation type.

"""
union Translate @union(resolveType: "JBernavaPrah\\\LighthouseTranslation\\\UnionTranslateResolveType")
 = Translation | RawTranslation 
GRAPHQL
        );
    }

    static function rawTranslationType(): ObjectTypeDefinitionNode
    {
        return Parser::objectTypeDefinition(/** @lang GraphQL */ <<<GRAPHQL
type RawTranslation {
    data: [Translation!]! @field(resolver: "JBernavaPrah\\\LighthouseTranslation\\\RawTranslationDataResolver")
}
GRAPHQL
        );
    }

    static function translationType(): ObjectTypeDefinitionNode
    {
        return Parser::objectTypeDefinition(/** @lang GraphQL */ <<<GRAPHQL
type Translation {
     lang: String! 
     text: String!
}
GRAPHQL
        );
    }

    static function localizeClientDirectiveType(): DirectiveDefinitionNode
    {
        return Parser::directiveDefinition(/** @lang GraphQL */ '
"""
Use this directive to translate the field, and subfields with localized translations.
"""
directive @localize(lang: String!) on FIELD | INLINE_FRAGMENT | FRAGMENT_SPREAD | FIELD_DEFINITION
');
    }
}
