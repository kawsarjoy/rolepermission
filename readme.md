# Roles And Permissions For Laravel 5.7.

A Simple package for handling roles and permissions in Laravel.

- [Installation](#installation)
    - [Composer](#composer)
    - [Service Provider](#service-provider)
    - [Config File](#config-file)
    - [Permissible Trait](#permissible-trait)
    - [Migrations and Seeds](#migrations-and-seeds)
- [Usage](#usage)
    - [Creating Roles](#creating-roles)
    - [Attaching, Detaching and Syncing Roles](#attaching-detaching-and-syncing-roles)
    - [Assign a user role to new registered users](#assign-a-user-role-to-new-registered-users)
    - [Checking For Roles](#checking-for-roles)
    - [Creating Permissions](#creating-permissions)
    - [Attaching, Detaching and Syncing Permissions](#attaching-detaching-and-syncing-permissions)
    - [Checking For Permissions](#checking-for-permissions)
    - [Blade Extensions](#blade-extensions)
    - [Middleware](#middleware)
- [Config File](#config-file)
- [More Information](#more-information)
- [Opening an Issue](#opening-an-issue)
- [License](#license)

---

## Installation

This package is very easy to set up. There are only couple of steps.

### Composer

Pull this package in through Composer
```
composer require kawsarjoy/rolepermission
```

### Service Provider
* Laravel 5.5 and up
Uses package auto discovery feature, no need to edit the `config/app.php` file.

* Laravel 5.4 and below
Add the package to your application service providers in `config/app.php` file.

```php
'providers' => [

    ...

    /**
     * Third Party Service Providers...
     */
    KawsarJoy\RolePermission\RolePermissionServiceProvider::class,

],
```

### Config File

Publish the package config file and views to your application. Run these commands inside your terminal.

    php artisan vendor:publish

### Permissible Trait

1. Include `Permissible` trait and also implement `Permissible` contract inside your `User` model. See example below.

2. Include `use KawsarJoy\RolePermission\Permissible;` in the top of your `User` model below the namespace and implement the `Permissible` trait. See example below.

Example `User` model Trait And Contract:

```php

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use KawsarJoy\RolePermission\Permissible;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use Permissible;

    // rest of your model ...
}

```

### Migrations and seeds
> This uses the default users table which is in Laravel. You should already have the migration file for the users table available and migrated.

1. Setup the needed tables:

    `php artisan migrate`

2. Update `database\seeds\DatabaseSeeder.php` to include the seeds. See example below.


```php
<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

            $this->call('RolesTableSeeder');
            $this->call('PermissionsTableSeeder');
            $this->call('ConnectRelationshipsSeeder');
            //$this->call('UsersTableSeeder');

        Model::reguard();
    }
}

```

3. Seed an initial set of Permissions, Roles, and Users with roles.

```
composer dump-autoload
php artisan db:seed
```

#### Roles Seeded
|Property|Value|
|----|----|
|Name| admin|
|Description| Admin Role|

|Property|Value|
|----|----|
|Name| user|
|Description| User Role|

#### Permissions Seeded:
|Property|Value|
|----|----|
|name|create-user|
|description|Can view users|

|Property|Value|
|----|----|
|name|view-user|
|description|Can create new users|

|Property|Value|
|----|----|
|name|edit-user|
|description|Can edit users|

|Property|Value|
|----|----|
|name|delete-user|
|description|Can delete users|


### And that's it!

---

## Usage

### Creating Roles

```php
use KawsarJoy\RolePermission\Models\Role;

$adminRole = Role::create([
    'name' => 'admin',
    'description' => 'Admin User',
]);

$authorRole = Role::create([
    'name' => 'author',
    'description' => 'Author User',
]);
```

### Attaching, Detaching and Syncing Roles

It's really simple. You fetch a user from database and call `attachRole` method. There is `BelongsToMany` relationship between `User` and `Role` model.

```php
use App\User;

$user = User::find($id);

$user->roles()->sync([1,2]); // you have to pass array of ids
```

### Assign a user role to new registered users

You can assign the user a role upon the users registration by updating the file `app\Http\Controllers\Auth\RegisterController.php`.
You can assign a role to a user upon registration by including the needed models and modifying the `create()` method to attach a user role. See example below:

* Update the top of `app\Http\Controllers\Auth\RegisterController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use jeremykenedy\LaravelRoles\Models\Role;
use jeremykenedy\LaravelRoles\Models\Permission;
use Illuminate\Foundation\Auth\RegistersUsers;

```


### Checking For Roles

You can now check if the user has required role.

```php
if ($user->hasRole('admin')) { // you have to pass a name
    //
}
```

And of course, there is a way to check for multiple roles:

```php
if ($user->hasRole(['admin', 'moderator'])) {
    // The user has at least one of the roles
}

```

### Creating Permissions

It's very simple thanks to `Permission` model.

```php
use KawsarJoy\RolePermission\Models\Permission;

$createUsersPermission = Permission::create([
    'name' => 'create-user',
    'description' => 'Can create user', // optional
]);

$deleteUsersPermission = Permission::create([
    'name' => 'delete-user',
    'description' => 'Can delete user', // optional
]);
```

### Attaching, Detaching and Syncing Permissions

You can attach permissions to a role

```php
use App\User;
use KawsarJoy\RolePermission\Models\Role;

$role = Role::find($roleId);
$role->permissions()->sync([1,2,3]); // permissions attached to a role
```

### Checking For Permissions

```php
if ($user->hasPermission('create-user') { // you have to pass a name
    //
}

```

You can check for multiple permissions the same way as roles.

### Blade Extensions

There are four Blade extensions. Basically, it is replacement for classic if statements.

```php
@role('admin') // @if(Auth::check() && Auth::user()->hasRole('admin'))
    // user has admin role
@endrole

@permission('edit-articles') // @if(Auth::check() && Auth::user()->hasPermission('edit-articles'))
    // user has edit articles permissison
@endpermission

@role('admin|moderator', true) // @if(Auth::check() && Auth::user()->hasRole('admin|moderator', true))
    // user has admin and moderator role
@else
    // something else
@endrole
```

### Middleware

This package comes with `CheckRole`, `CheckPermission` middleware.These middleware will be autoload by the RolePermissionServiceProvider. You dont't have to add again in your Http\Kernel

Now you can easily protect your routes.

```php
Route::get('/', function () {
    //
})->middleware('roles:admin');

Route::get('/', function () {
    //
})->middleware('permissions:edit-user');

//Permissions middleware will chack the permissions based on the resource route names eg. [users/create => [users.create | [prefix].users.create]]
Route::Resource('users','UserController')->middleware('permissions');

Route::group(['middleware' => ['roles:admin']], function () {
    //
});

Route::group(['middleware' => ['roles:admin|author']], function () {
    //
});

Route::group(['middleware' => ['permissions:create-user']], function () {
    //
});


Route::group(['middleware' => ['permissions:create-user|edit-user']], function () {
    //
});

```

It throws 403 http error and load 403.blade.php view file which is come with the package. If you want to modify the view then you have to publish the view by running this command.

```
php artisan vendor:publish --tag=views
```

---
## Credit Notes
This package readme file is inspired from [laravel-roles](https://github.com/jeremykenedy/laravel-roles)


## Config File
You can change connection for models, slug separator, models path and there is also a handy pretend feature. Have a look at config file for more information.

## More Information
For more information, please have a look at [HasRoleAndPermission](https://github.com/kawsarjoy/rolepermission/blob/master/src/Permissible.php) trait.


## Opening an Issue
Before opening an issue there are a couple of considerations:
* A **star** on this project shows support and is way to say thank you to all the contributors. If you open an issue without a star, *your issue may be closed without consideration.* Thank you for understanding and the support.
* **Read the instructions** and make sure all steps were *followed correctly*.
* **Check** that the issue is not *specific to your development environment* setup.
* **Provide** *duplication steps*.
* **Attempt to look into the issue**, and if you *have a solution, make a pull request*.
* **Show that you have made an attempt** to *look into the issue*.
* **Check** to see if the issue you are *reporting is a duplicate* of a previous reported issue.
* **Following these instructions show me that you have tried.**
* If you have a questions send me an email to engabukawsar@gmail.com
* Please be considerate that this is an open source project that I provide to the community for FREE when opening an issue. 

## License
This package is free software distributed under the terms of the MIT license.