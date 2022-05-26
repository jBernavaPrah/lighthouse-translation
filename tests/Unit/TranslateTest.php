<?php

namespace JBernavaPrah\LighthouseTranslation\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use JBernavaPrah\LighthouseTranslation\Translate;

class TranslateTest extends UnitTest
{

    /**
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    function test_translate_cast_correctly()
    {

        $model = new class extends Model {

        };

        $castingClass = Translate::castUsing([]);

        $locale = [
            "lang" => "it",
            "text" => "abc"
        ];

        $setResult = $castingClass->set($model, "name", [$locale], []);

        $this->assertEquals(json_encode([$locale]), $setResult);

        $expected = new Translate($locale);
        $expected->setModel($model);

        $this->assertEquals([$expected], $castingClass->get($model, "name", $setResult, []));


    }

}