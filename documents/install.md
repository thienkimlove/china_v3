## Install

* We using `Laravel 7.x` with `Backpack 4.1`


* DB

`mysql -uroot -ptieungao -e "CREATE DATABASE china_v3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`

* Follow [Backpack 4.1 Install](https://backpackforlaravel.com/docs/4.1/installation)

* Commands

```text
composer require backpack/crud:"4.1.x-dev as 4.0.99"
composer require backpack/generators --dev
composer require laracasts/generators --dev
php artisan backpack:install
php artisan backpack:user
composer require backpack/permissionmanager


```

* Basic about BackPack

```text
https://backpackforlaravel.com/docs/4.1/getting-started-basics
```

* Install Addon

`https://backpackforlaravel.com/docs/4.1/install-optionals`

* Currently for working demo we will allow user to login and register if localhost and check them is admin.



