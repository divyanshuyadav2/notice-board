<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\UserOrganization;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.layout', function ($view) {

            if (!session()->has('User_UIN')) {
                $view->with('organizations', collect());
                return;
            }

            $organizations = UserOrganization::with('organization')
                ->where('User_UIN', session('User_UIN'))
                ->get()
                ->pluck('organization')
                ->filter();

            $view->with('organizations', $organizations);
        });
    }
}
