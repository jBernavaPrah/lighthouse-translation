<?php

namespace JBernavaPrah\LighthouseTranslation;

class RawTranslationDataResolver
{
    public function __invoke(array $translations): array
    {
        return $translations;
    }

}
