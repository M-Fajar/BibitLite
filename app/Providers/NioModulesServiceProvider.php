<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use NioModules\Paypal\PaypalModule;

class NioModulesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try{
            if (!empty($modules = available_modules('mod'))) {
                foreach ($modules as $module) {

                    if (file_exists($provider = base_path(implode(DIRECTORY_SEPARATOR, ['niomodules', $module, 'Provider', 'RouteServiceProvider.php'])))) {
                        $this->app->register("\NioModules\\$module\Provider\RouteServiceProvider");
                    }

                    if (file_exists($views = base_path(implode(DIRECTORY_SEPARATOR, ['niomodules', $module, 'Views'])))) {
                        $this->loadViewsFrom($views, $module);
                    }

                    if (file_exists($config = base_path(implode(DIRECTORY_SEPARATOR, ['niomodules', $module, 'Config', 'module.php'])))) {
                        $this->mergeConfigFrom($config, 'modules');
                    }

                    if (file_exists($migrations = base_path(implode(DIRECTORY_SEPARATOR, ['niomodules', $module, 'Database', 'migrations'])))) {
                        $this->loadMigrationsFrom($migrations);
                    }

                    if (class_exists($moduleLoader = "\\NioModules\\{$module}\\{$module}Module")) {
                        $this->app->singleton(strtolower($module), function() use ($moduleLoader) {
                            return new $moduleLoader();
                        });
                    }
                }
            }
        } catch (\Exception $e) {
            if (env('APP_DEBUG', false)) {
                save_error_log($e, 'module-service');
            }
        }
    }
}
