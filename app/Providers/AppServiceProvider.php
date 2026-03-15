<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Page;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Paginator::useBootstrap();

           // Optional: load shared data(get all information)
        $page_data = Page::where('id', 1)->first();

         // Share data globally with all views
        view()->share('global_page_data', $page_data);
    }
}
