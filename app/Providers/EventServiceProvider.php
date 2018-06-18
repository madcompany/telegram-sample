<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],

        
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
			// add your listeners (aka providers) here
	        'SocialiteProviders\Telegram\TelegramExtendSocialite@handle',
		],
        
    ];
    
    
}
