<?php

namespace App\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // MariaDB/MySQL 5.7 requires string keys to be at most 191 chars in utf8mb4
        Builder::defaultStringLength(191);
    }
}
