<?php

namespace JBernavaPrah\LighthouseTranslation;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Spatie\DataTransferObject\DataTransferObject;
use function Safe\json_decode;

class Translate extends DataTransferObject implements Castable
{

    protected Model $model;
    public string $lang;
    public string $text;

    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes {
            public function get($model, string $key, $value, array $attributes)
            {
                if (is_null($value)) {
                    return null;
                }

                $value = json_decode($value, assoc: true);
                return array_map(function ($value) use ($model) {
                    return (new Translate($value))->setModel($model);
                }, $value);
            }

            public function set($model, string $key, $value, array $attributes)
            {
                if (is_null($value)) {
                    return null;
                }

                return json_encode(array_map(function ($value) {
                    return (new Translate($value))->toArray();
                }, $value));
            }
        };
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return Translate
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }
}
