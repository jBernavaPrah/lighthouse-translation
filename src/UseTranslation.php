<?php

namespace JBernavaPrah\LighthouseTranslation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @mixin Model
 */
trait UseTranslation
{

    abstract protected function translationColumns(): array;

    public function initializeUseTranslation(): void
    {

        $casts = Collection::wrap($this->translationColumns())
            ->mapWithKeys(fn($column) => [
                $column => Translate::class,
            ])->toArray();

        $this->casts = array_merge($this->casts, $casts);

    }


}