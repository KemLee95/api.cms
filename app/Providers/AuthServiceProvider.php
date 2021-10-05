<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',

        \App\Models\Post::class => \App\Policies\PostPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Passport::routes();
        Passport::tokensCan([
            'admin' => 'Scope for Admin',
            'user' => 'Scope for User'
        ]);
        Passport::tokensExpireIn(now()->addDay(30));
        Passport::refreshTokensExpireIn(now()->addDay(30));
        Passport::personalAccessTokensExpireIn(now()->addYear(1));

        VerifyEmail::toMailUsing(function($notifiable, $url){
            return (new MailMessage)
                    -> subject('Verify Email Address')
                    -> line('Click the button below to verify your email address.')
                    -> action('Verify Email Address', $url);
        });
    }
}
