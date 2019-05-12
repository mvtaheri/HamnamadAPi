<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/8/2019
 * Time: 11:42 AM
 */

namespace App\Providers;

use App\Model\Comment;
use App\Model\Feed;
use App\Model\User;
use mmghv\LumenRouteBinding\RouteBindingServiceProvider as BaseServiceProvider;

class RouteBindingServiceProvider extends BaseServiceProvider
{
    /**
     * Boot the service provider
     */
    public function boot()
    {
        // The binder instance
        $binder = $this->binder;

        // Here we define our bindings
        $binder->bind('comment', Comment::class);
        $binder->bind('feed', Feed::class);
        $binder->bind('user', User::class);
    }
}
