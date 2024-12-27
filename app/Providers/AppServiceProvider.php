<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		Blade::directive('formatMoney', static function ($money) {
			return "<?php echo (new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY))->format($money); ?>";
		});

		Blade::directive('formatNumber', static function ($number) {
			return "<?php echo (new NumberFormatter(app()->getLocale(), NumberFormatter::DECIMAL_SEPARATOR_SYMBOL))->format($number); ?>";
		});
    }
}
