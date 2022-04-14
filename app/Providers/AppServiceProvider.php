<?php

namespace App\Providers;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Validation\BailingValidator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            //$this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $cookie = Cookie::get('theme');
        $mode = '';

        if($cookie) {
            View::share('themeMode', CookieValuePrefix::remove(Crypt::decryptString((Cookie::get('theme')))));
        } else {

            try {

                $settingsLoaded = Schema::hasTable('settings');

                if ($settingsLoaded) {

                    $darkEnabled = setting('general.dark_mode_status', false);

                    if ($darkEnabled) {

                        if (setting('general.default_dark_mode_status', false)) {
                            $mode = 'dark';
                        }
                    }
                }
            } catch (\Exception $e) {

            }

            View::share('themeMode', $mode);
        }

        /**
         * @var \Illuminate\Validation\Factory $factory
         */
        $factory = resolve(Factory::class);

        $factory->resolver(function (Translator $translator, array $data, array $rules, array $messages, array $customAttributes) {
            return new BailingValidator($translator, $data, $rules, $messages, $customAttributes);
        });
    }
}
