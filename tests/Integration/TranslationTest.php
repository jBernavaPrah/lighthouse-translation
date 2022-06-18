<?php

namespace JBernavaPrah\LighthouseTranslation\Tests\Integration;

use JBernavaPrah\LighthouseTranslation\Translate;
use Nuwave\Lighthouse\Testing\MocksResolvers;
use Nuwave\Lighthouse\Testing\UsesTestSchema;

class TranslationTest extends IntegrationTest
{

    use UsesTestSchema;
    use MocksResolvers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestSchema();
    }

    public function test_translation_not_found(): void
    {
        $this->mockResolver([
            new Translate([
                "text" => "ABCit",
                "lang" => "it"
            ]),
            new Translate([
                "text" => "ABCen",
                "lang" => "en"
            ])
        ], "first");

        $this->schema = /** @lang GraphQL */
            '
        type Query {
            foo: Translate @mock(key: "first") 
        }
        ';

        $this->graphQL(/** @lang GraphQL */ "query  {
            foo @localize(lang: \"fg\")  {
                   ... on Translation {
                    text
                    lang
                 }
            }
}")
            ->assertJsonFragment([
                'data' => [
                    'foo' => null,
                ],
            ]);

    }

    public function testTranslateCorrectlyReturnDefaultRawTranslationType(): void
    {

        $this->mockResolver([
            new Translate([
                "text" => "ABCit",
                "lang" => "it"
            ]),
            new Translate([
                "text" => "ABCen",
                "lang" => "en"
            ])

        ]);

        $this->schema = /** @lang GraphQL */
            '
        type Query {
            foo: Translate @mock
        }
        ';

        $this->graphQL(/** @lang GraphQL */ "query {
foo {
    ... on RawTranslation {
    data {
        text
        lang
    }
    }
}
}")
            ->assertJsonFragment([
                'data' => [
                    'foo' => [
                        "data" => [
                            [
                                'text' => "ABCit",
                                "lang" => "it"
                            ],
                            [
                                'text' => "ABCen",
                                "lang" => "en"
                            ]
                        ]
                    ],
                ],
            ]);
    }

    public function testMultiLevelDirectiveChange(): void
    {

        $this->mockResolver([
            "name" => [
                new Translate([
                    "text" => "ABCit",
                    "lang" => "it"
                ]),
                new Translate([
                    "text" => "ABCen",
                    "lang" => "en"
                ])
            ],
            "surname" => [
                new Translate([
                    "text" => "ABCit",
                    "lang" => "it"
                ]),
                new Translate([
                    "text" => "ABCen",
                    "lang" => "en"
                ])
            ]
        ], "first");

        $this->schema = /** @lang GraphQL */
            '
        
        type FooResponse {
            name: Translate 
            surname: Translate
        }
        
        type Query {
            foo: FooResponse @mock(key: "first") 
        }
        ';

        $this->graphQL(/** @lang GraphQL */ "query  {
            foo @localize(lang: \"en\")  {
                   
                surname @localize(lang: \"it\")  {
                 ... on Translation {
                    text
                    lang
                 }
                }
            
                name  {
                ... on Translation {
                    text
                    lang
                }
                }
            }

}")
            ->assertJsonFragment([
                'data' => [
                    'foo' => [
                        "name" => [
                            'text' => "ABCen",
                            "lang" => "en"
                        ],
                        "surname" => [
                            'text' => "ABCit",
                            "lang" => "it"
                        ]
                    ],
                ],
            ]);

    }

    public function testCallClientDirectiveLocalize(): void
    {

        $this->mockResolver([
            "name" => [
                new Translate([
                    "text" => "ABCit",
                    "lang" => "it"
                ]),
                new Translate([
                    "text" => "ABCen",
                    "lang" => "en"
                ])
            ]
        ], "first");

        $this->mockResolver("ABC", "second");

        $this->mockResolver([
            "name" => [
                new Translate([
                    "text" => "ABCit",
                    "lang" => "it"
                ]),
                new Translate([
                    "text" => "ABCen",
                    "lang" => "en"
                ])
            ]
        ], "third");

        $this->schema = /** @lang GraphQL */
            '
        
        type FooResponse {
            name: Translate 
        }
        
        type BarResponse {
            name: Translate 
        }


        type Query {
            foo: FooResponse  @mock(key: "first") 
            other: String  @mock(key: "second")
            bar: BarResponse @mock(key: "third")
        }
        ';

        $this->graphQL(/** @lang GraphQL */ "query {
foo @localize(lang: \"it\")  {
    name  {
    ... on Translation {
        text
        lang
    }
    }
}
other
bar {
    name {
        ... on RawTranslation {
            data {
            text
            lang
            }
        }
    }
}
}")
            ->assertJsonFragment([
                'data' => [
                    'foo' => [
                        "name" => [
                            'text' => "ABCit",
                            "lang" => "it"
                        ]
                    ],
                    "other" => "ABC",
                    "bar" => [
                        "name" => [
                            "data" => [
                                [
                                    'text' => "ABCit",
                                    "lang" => "it"
                                ],
                                [
                                    'text' => "ABCen",
                                    "lang" => "en"
                                ]
                            ]
                        ]
                    ]
                ],
            ]);

    }

}