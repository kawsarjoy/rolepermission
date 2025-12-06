<?php
    namespace KawsarJoy\RolePermission;

    use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Facades\Gate;
    use Illuminate\Console\Events\CommandFinished;
    use Illuminate\Filesystem\Filesystem;
    
    class RolePermissionServiceProvider extends ServiceProvider
    {
        public function boot()
        {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
            
            if(config('permissions-config.do-migration')){
                $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
            }

            $this->loadViewsFrom(__DIR__.'/resources/views', 'rolepermission');

            $this->publishes([
                __DIR__.'/resources/views' => resource_path('views/vendor/rolepermission')
            ], 'views');
            $this->publishes([
                __DIR__.'/config/permissions-config.php' => config_path('permissions-config.php'),
            ], 'config');
            $this->publishes([
                __DIR__.'/Database/migrations' => database_path('migrations'),
            ], 'migration');

            $this->publishes([
                __DIR__.'/Database/migrations' => database_path('migrations-tmp'),
            ], 'migration-latest');

            $this->app['router']->aliasMiddleware('roles', \KawsarJoy\RolePermission\Http\Middleware\CheckRole::class);
            $this->app['router']->aliasMiddleware('permissions', \KawsarJoy\RolePermission\Http\Middleware\CheckPermission::class);

            $this->registerBladeDirectives();

            $this->registerGates();

            // Listen for console commands finishing
            $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {
                // Some Laravel versions set $event->command to the command name string,
                // others set it to the command object. Check both.
                $isVendorPublish = false;

                if (is_string($event->command) && $event->command === 'vendor:publish') {
                    $isVendorPublish = true;
                } elseif (is_object($event->command)) {
                    // Class name check (VendorPublishCommand exists in Laravel)
                    $class = get_class($event->command);
                    if (str_contains($class, 'VendorPublish') || str_ends_with($class, 'VendorPublishCommand')) {
                        $isVendorPublish = true;
                    }
                }

                if (! $isVendorPublish) {
                    return;
                }

                // Defensive: ensure $event has input (some envs might differ)
                if (! property_exists($event, 'input') || ! $event->input) {
                    return;
                }

                // Get tags option — may be string, array, or null
                $tags = $event->input->getOption('tag');

                if (! $tags) {
                    \Log::info('KawsarJoy\RolePermission with No Tag');
                    return;
                }

                // Normalize to array of tag strings
                $tagsArr = is_array($tags) ? $tags : array_map('trim', explode(',', (string) $tags));

                if (in_array('migration-latest', $tagsArr, true)) {
                    // Now run the timestamp handler
                    $tmp = database_path('migrations-tmp');
                    $this->handleTimestampedMigrations($tmp);
                }else{
                    \Log::info('KawsarJoy\RolePermission with Tag'.json_encode($tagsArr));
                }
            });

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
            	if(!config('permissions-config.rolepermission-enable'))
            		return "<?php if (true): ?>";
                return "<?php if (Auth::check() && Auth::user()->hasRole({$expression})): ?>";
            });

            $blade->directive('endrole', function () {
                return '<?php endif; ?>';
            });

            $blade->directive('permission', function ($expression) {
            	if(!config('permissions-config.rolepermission-enable'))
            		return "<?php if (true): ?>";
                return "<?php if (Auth::check() && Auth::user()->hasPermission({$expression})): ?>";
            });

            $blade->directive('endpermission', function () {
                return '<?php endif; ?>';
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

        /**
         * Copy migration stubs from the temporary publish folder to database/migrations
         * with freshly-generated timestamped filenames. Runs only when $tmp exists.
         *
         * @param string $tmpPath
         * @return void
         */
        protected function handleTimestampedMigrations(string $tmpPath)
        {
            $filesystem = new Filesystem;

            if (! $filesystem->exists($tmpPath)) {
                // Not publishing with the timestamp tag — do nothing
                return;
            }

            // Get files from temp folder
            $files = $filesystem->files($tmpPath);

            if (empty($files)) {
                // No files to process
                $filesystem->deleteDirectory($tmpPath);
                return;
            }

            // We'll ensure unique timestamps across files (increment seconds or append an index)
            $now = now(); // Illuminate\Support\Carbon
            $sec = (int)$now->format('U'); // seconds since epoch
            $index = 0;

            foreach ($files as $file) {
                $index++;

                // Original filename (without any directory)
                $originalFilename = $file->getFilename();

                // Make a safe base name to check duplicates: keep the filename part after any existing timestamp
                // Remove leading timestamp-like patterns "YYYY_MM_DD_HHMMSS_" if present
                $basename = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_?/', '', $originalFilename);

                // If a migration with this basename already exists in database/migrations, skip it
                $existingMatches = glob(database_path('migrations/*_' . $basename));
                if (! empty($existingMatches)) {
                    // Already published; skip to avoid duplicates
                    continue;
                }

                // Create a unique timestamp string: Y_m_d_His plus an index to guarantee uniqueness
                // We use sec + index to avoid duplicate seconds, then format to Laravel timestamp.
                $timestamp = date('Y_m_d_His', $sec + $index - 1);

                // If you want to strictly guarantee uniqueness even within the same second,
                // you can append an incremental suffix:
                $uniqueSuffix = $index > 1 ? "_{$index}" : '';

                $newFileName = $timestamp . $uniqueSuffix . '_' . $basename;

                // Copy file into final migrations folder
                $filesystem->copy($file->getRealPath(), database_path('migrations/' . $newFileName));
            }

            // Clean up temporary folder so handler won't run again
            $filesystem->deleteDirectory($tmpPath);
        }

    }