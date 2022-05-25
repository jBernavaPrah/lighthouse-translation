# Lighthouse Translation

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jBernavaPrah/lighthouse-translation/run-tests?label=tests)](https://github.com/jBernavaPrah/lighthouse-translation/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/jBernavaPrah/lighthouse-translation/badge.svg?branch=main)](https://coveralls.io/github/jBernavaPrah/lighthouse-translation?branch=main)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/jBernavaPrah/lighthouse-translation.svg?style=flat-square)](https://packagist.org/packages/jBernavaPrah/lighthouse-translation)
[![Total Downloads](https://img.shields.io/packagist/dt/jBernavaPrah/lighthouse-translation.svg?style=flat-square)](https://packagist.org/packages/jBernavaPrah/lighthouse-translation)

### Is not PRODUCTION READY. 


- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)

## Requirements

- [laravel/laravel:^9.0](https://github.com/laravel/laravel)
- [nuwave/lighthouse:^5.5](https://github.com/nuwave/lighthouse)

## Installation

#### 1. Install using composer:

```bash
composer require jBernavaPrah/lighthouse-translation
```

#### 2. UseTranslation

Apply the `JBernavaPrah\Translation\UseTranslation` trait to your models.

```php

use JBernavaPrah\UseTranslation ;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \JBernavaPrah\LighthouseTranslation\Translate $name
 * 
 */
class Item extends Model 
{
    use UseTranslation;
    
    public function translationColumns(): array{
        return ["name"]
    }
    
    
}

```

#### 3. Declare field type as `Translate` on GraphQL schema

```graphql
type Item {

    name: Translate!

    # ...

}
```

## Usage

### Enable Translation

The directive `localize` accepts as a parameter the lang to use to return the 
localized data. 

If is not founded on the column, null is returned.

```graphql
query {
    item(id: 1) @localize(lang: "en") {
        name {
            ...on Localized{
                lang
                text
            }
        }
    }
}
```

In case the directive is not used on that query/mutation, the RawTranslation type is returned.

```graphql
query {
    item(id: 1) {
        name {
            ... on RawTranslation {
                data {
                    lang
                    text
                }
            }
        }
    }
}
```

### Credits:

A lot of ideas came thanks to [daniel-de-wit/lighthouse-sanctum](https://github.com/daniel-de-wit/lighthouse-sanctum). Big thanks to him and his beautiful code!  
Also the authors of [nuwave/lighthouse](https://github.com/nuwave/lighthouse) did a great job on the documentation and code.  
