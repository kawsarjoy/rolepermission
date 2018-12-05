<?php
    namespace KawsarJoy\RolePermission;

    use Illuminate\Support\ServiceProvider;

    class RolePermissionServiceProvider extends ServiceProvider
    {
        public function boot()
        {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');

            $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

            $this->loadViewsFrom(__DIR__.'/resources/views', 'rolepermission');

            $this->publishes([
                __DIR__.'/resources/views' => resource_path('views/vendor/rolepermission'),
            ]);

            $this->app['router']->aliasMiddleware('roles', \KawsarJoy\RolePermission\Http\Middleware\CheckRole::class);

            $this->app['router']->aliasMiddleware('permissions', \KawsarJoy\RolePermission\Http\Middleware\CheckPermission::class);

        }

        public function register()
        {

        }
    }