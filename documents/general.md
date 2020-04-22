## Here to show general notes.

* After install and setup `permissions backpack`.

* Read about backpack 4.1 syntax.

* Add more field to users.

* Next we go to install `jwt` and create `ApiController`.

* We using JWT for `user`.

Commands

```text
composer require tymon/jwt-auth 
 php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
# edit config/jwt.php
php artisan jwt:secret
```

* Extra step to work with jwt

Add to `app/Http/Kernel.php`

```text
'auth.jwt'  =>  \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    ];
```

Add to `app/Exceptions/Handler.php`

```text
if ($request->isJson() && $exception instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Token',
            ], 500);
        }

        if ($request->isJson() && $exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Token',
            ], 500);
        }

        if ($request->isJson() && $exception instanceof TokenExpiredException) {
            return response()->json([
                'success' => false,
                'message' => 'Token Expired',
            ], 500);
        }

        if ($request->isJson() && $exception instanceof TokenInvalidException) {
            return response()->json([
                'success' => false,
                'message' => 'Token Invalid',
            ], 500);
        }
```

Using in `routes/api.php`

```text
Route::group(['middleware' => ['auth.jwt']], function () {
        Route::get('logout', 'AuthController@logout');
```


* Read carefully about Jwt with Laravel.

`https://jwt-auth.readthedocs.io/en/docs/quick-start/`

* OK now we go to create `cart`, `payment`, `order`,

`detail`, `balance` and `shop`

* We make get error with ` php artisan backpack:crud order`

please referrer to `backpack_upgrade`

* After create all migrations, we go create many backpacks and move logic from v2 to here.

* Next we test the api with postman.


