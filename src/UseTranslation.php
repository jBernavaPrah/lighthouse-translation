<?php

namespace JBernavaPrah\LighthouseTranslation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @mixin Model
 */
trait UseTranslation
{

    /**
     * Indicate what are the columns that will have the translations.
     * Those columns will be automatically casted by Translate class.
     *
     * @return array
     */
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