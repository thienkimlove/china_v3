### Something new about code in backpack 4.1


* Customize view backpack

`https://backpackforlaravel.com/docs/4.1/crud-how-to`

* Using `CRUD::` instead of `$this->crud` in controller.

Must add `use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;`

* If want to customize one view for one resource

```text
alternatively, if you only want to change a blade file for one CRUD, 

you can use the methods below in your setup() method, 

to change a particular view:

$this->crud->setShowView('your-view');
$this->crud->setEditView('your-view');
$this->crud->setCreateView('your-view');
$this->crud->setListView('your-view');
$this->crud->setReorderView('your-view');
$this->crud->setDetailsRowView('your-view');

```

* Add Extra CRUD Routes

```text
Starting with Backpack\CRUD 4.0, 

routes are defined inside the Controller, 

in methods that look like setupOperationNameRoutes();
 
 you can use this naming convention to setup extra routes,
  
  for your custom operations:

protected function setupModerateRoutes($segment, $routeName, $controller) {
  Route::get($segment.'/{id}/moderate', [
      'as'        => $routeName.'.moderate',
      'uses'      => $controller.'@moderate',
      'operation' => 'moderate',
  ]);

  Route::post($segment.'/{id}/moderate', [
      'as'        => $routeName.'.saveModeration',
      'uses'      => $controller.'@saveModeration',
      'operation' => 'moderate',
  ]);
}
If you want the route to point to a different controller, you can add the route in routes/backpack/custom.php instead.
```

* Publish a column / field / filter / button and modify it

Note : please change the name, so this not affected origin.


* Inside CrudControllers, Backpack 4.1:

no longer defines `$this->request`

still defines `$this->crud->request` (aka `CRUD::request`)
 
but uses getters and setters to work with it (`$this->crud->getRequest()` 

and `$this->crud->setRequest())`;

```text
If you have $this->request anywhere in your CrudControllers custom logic,
 
 please replace it with either Laravel's request() helper or with $this->crud->getRequest().
 
If you have $this->crud->request anywhere inside your custom CrudController logic,
 
 please replace it with either $this->crud->getRequest() or
  
 $this->crud->setRequest() depending on what your intention is.
```

* wrapperAttributes

```text
Inside CrudControllers, if you've used wrapperAttributes on fields, 

please note that it's now called wrapper.
 
 Please search & replace wrapperAttributes 
 
 with wrapper in your CrudControllers.
```

* we're now using the "line awesome syntax" (`la la-home`), to prevent conflicts when both fonts are used at the same time;

* In order to be able to use the new fluent syntax for Widgets, you should make sure all main admin panel views (ex: dashboard, create, update, etc) extend the blank template:
```
//from 
- @extends('backpack::layouts.top_left')
- @extends(backpack_view('layouts.top_left'))

// to
+ @extends(backpack_view('blank'))
// or
+ @extends('backpack::blank')

```

* Cache
  Step 18. Clear your app's cache:
```text
  php artisan config:clear
  php artisan cache:clear
  php artisan view:clear
```

* Create a custom button

`https://backpackforlaravel.com/docs/4.1/crud-buttons`

* Add the icon to sidebar `la la-` instead of `fa fa-`


* Create a custom view

We find the origin view at `vendor/backpack/crud/src/resources/views/crud/list.blade.php`

copy it to `resources/views/vendor/backpack/crud/order_list.blade.php`

and then we can modify it to fit the requirements.

a) Step 1 we find the view for the old theme at `orderchina`

`resources/views/v2/list_order.blade.php`

and copy its listed all status html there.

and put in `crud Controller`

```text
$this->crud->setListView('vendor.backpack.crud.order_list');
```

Go to `https://backstrap.net/base/list-group.html` view some html elements code and to there.

Or we can create a widget

```text
Widgets (aka cards, aka charts, aka graphs) provide a simple way
 
 to insert blade files into admin panel pages. 
 
 You can use them to insert cards, charts, notices or custom content into pages.
```

We can make a widget with view like that

```text
View
Loads a blade view from a location you specify. Any attributes your give it will be available in the $widget variable inside that view.

[
    'type'        => 'view',
    'view'        => 'path.to.custom.view',
    'someAttr'    => 'some value',
]
It helps load blade files that are not specifically created to be widgets, that live in a different path than resources/views/vendor/backpack/base/widgets, as if they were widgets.
```

See how i create `resources/views/widgets/order_status.blade.php` and add to the list view with 

```text
Widget::add([
    'type'        => 'view',
    'view'        => 'widgets.order_status',
    'name'    => 'Status',
])->to('before_content');


```

* Next we can customize column display for each field in order.

* Add external css and js

We can copy external `css` and `js`

and add directly using `config/backpack/base.php`

`https://backpackforlaravel.com/docs/4.1/base-how-to`

* Add checkbox at first column 

`$this->crud->enableBulkActions();`