<?php

namespace JBernavaPrah\LighthouseTranslation\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use JBernavaPrah\LighthouseTranslation\Translate;

class TranslatorService
{

    protected array $translations;
    protected ?string $locale = null;

    /**
     * The callback that should be used to create the reset password URL.
     *
     * @var (\Closure(): string)|null
     */
    public static $getLocaleCallback;

    public function __construct(protected Application $app)
    {

    }

    public function setTranslations(array $translations): self
    {
        $this->translations = $translations;
        return $this;
    }

    public function selectTranslation(): ?Translate
    {

        return Collection::wrap($this->translations)
                ->first(fn(Translate $translate) => $translate->lang == $this->getLocale()) ?? null;
    }


    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {

        return $this->locale;
    }

    /**
     * @param string|null $locale
     * @return TranslatorService
     */
    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Set a callback that should be used when getting the current locale.
     *
     * @param \Closure(mixed, string): string  $callback
     * @return void
     */
    public static function getLocaleUsing($callback)
    {
        static::$getLocaleCallback = $callback;
    }

}