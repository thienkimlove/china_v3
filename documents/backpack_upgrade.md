### We using Backpack 4.1 as it beta

* Please check

`https://backpackforlaravel.com/docs/4.1/upgrade-guide`

* Install error with backpack 4.1

we remove and install as `composer require backpack/permissionmanager:"4.1.x-dev as 5.0"`
* New thing from backpack 4.1

`https://backpackforlaravel.com/docs/4.1/release-notes`

* Instead of using 

`$this->crud->addFields` and `$this->crud->addColumns`

we can using like `https://github.com/Laravel-Backpack/CRUD/pull/2513`

* Error with `php artisan backpack:crud`

i need to add 
```text
"ext-json": "*",
"laravel/helpers": "^1.1",
"ext-curl": "*"
```

to `composer.json` and then run `composer install && composer update` and run command again.

```text
Backpack itself is no longer using laravel/helpers. Instead of using helpers like str_slug() we're now doing Str::slug() everywhere. We recommend you do the same. But if you want to keep using string and array helpers, please add "laravel/helpers": "^1.1", to your composer's require section.

Step 5. Since we're no longer using laravel/helpers, we need the Str and Arr classes to be aliased. You should have already done this when upgrading to Laravel 5.8/6.x/7.x. But please make sure that in your config/app.php you have these aliases:

    'aliases' => [
        // ...
        'Arr'       => Illuminate\Support\Arr::class,
        // ..
        'Str'       => Illuminate\Support\Str::class,
        // ..
    ],
```
