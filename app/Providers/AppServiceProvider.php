<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\StudentInClassChanging;
use App\Listeners\ReCalculateStudentsInClass;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;

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
        Event::listen(
            StudentInClassChanging::class,
            ReCalculateStudentsInClass::class,
        );

        foreach(config('permission.basic') as $key=>$permission){
            Gate::define($key, function (User $user) use ($key) {
                return (in_array($key, $user->permissions) === true || $user->id == 1);
            });
        }
    }
}
