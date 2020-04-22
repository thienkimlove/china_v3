## After install Backpack Permission

we will have

```text
Please note:

this will make auth() return the exact same thing as backpack_auth() on Backpack routes;

you only need this if you want to use @can; 



you can just as well use @if(backpack_user()->can('read')), 

which does the exact same thing, but works 100% of the time;

when you add new roles and permissions, 

the guard that gets saved in the database will be "backpack";

[Optional] Disallow create/update on your roles or permissions after you define them, 

using the config file in config/backpack/permissionmanager.php.
 
 Please note permissions and roles are referenced in code using their name. 
 If you let your admins edit these strings and they do, 
 
 your permission and role checks will stop working.
```

* Install error with backpack 4.1

we remove and install as ` composer require backpack/permissionmanager:"4.1.x-dev as 5.0"
`

* Middleware `app/Http/Middleware/CheckIfAdmin.php`

will define which user can login to our backpack Admin.

Now we let all user can login.

* Add more field to users

```text
'code',
'username',
'address',
'phone',
'bank_name',
'bank_branch',
'bank_account_name',
'bank_account_number'
```

* After that we will customize `Backpack/PermissionManager`

defaut `vendor/backpack/permissionmanager/src/app/Http/Controllers/UserCrudController.php`

by create `app/Http/Controllers/Admin/CustomUserCrudController.php`

and extends this.

* Please note that the route set for permission manager is

in `vendor/backpack/permissionmanager/src/PermissionManagerServiceProvider.php`

so we need to create `routes/backpack/permissionmanager.php`

and override only route for `user`

```text
Route::crud('user', 'CustomUserCrudController');
```

OK next in `app/Http/Controllers/Admin/CustomUserCrudController.php` we add the code to extends.



