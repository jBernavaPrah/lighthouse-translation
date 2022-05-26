<?php

namespace JBernavaPrah\LighthouseTranslation\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use JBernavaPrah\LighthouseTranslation\Translate;
use JBernavaPrah\LighthouseTranslation\UseTranslation;

class TestUseTranslation extends UnitTest
{

    function test_use_translation_correctly_add_casts()
    {

        $class = new class extends Model {
            use UseTranslation;

            public function translationColumns(): array
            {
                return ["name"];
            }
        };

        $this->assertArrayHasKey("name", $class->getCasts());
        $this->assertEquals(Translate::class, $class->getCasts()["name"]);


    }

}