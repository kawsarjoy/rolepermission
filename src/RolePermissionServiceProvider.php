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
                __DIR__.'/resources/views' => resource_path('views/vendor/rolepermission')
            ], 'config');

            $this->publishes([
                __DIR__.'/config/permissions-config.php' => config_path('permissions-config.php'),
            ], 'views');




            $this->app['router']->aliasMiddleware('roles', \KawsarJoy\RolePermission\Http\Middleware\CheckRole::class);

            $this->app['router']->aliasMiddleware('permissions', \KawsarJoy\RolePermission\Http\Middleware\CheckPermission::class);


            $this->registerBladeDirectives();

            $this->registerGates();

        }

        public function register()
        {
            $this->mergeConfigFrom(
                __DIR__.'/config/permissions-config.php', 'permissions-config'
            );
        }

        /**
         * Register Blade Directives.
         *
         * @return void
         */
        protected function registerBladeDirectives()
        {
            $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();
            
            $blade->directive('role', function ($expression) {
                return "<?php if (Auth::check() && Auth::user()->hasRole({$expression})): ?>";
            });

            $blade->directive('permission', function ($expression) {
                return "<?php if (Auth::check() && Auth::user()->hasPermission({$expression})): ?>";
            });

        }

        /**
         * Register Gates.
         *
         * @return void
         */
        protected function registerGates()
        {
            Gate::define('roles', function ($user, $roles) {

                return $user->hasRole(explode('|', $roles));
            });

            Gate::define('permissions', function ($user, $permissions) {
                
                return $user->hasPermission(explode('|', $permissions));
            });
        }

    }