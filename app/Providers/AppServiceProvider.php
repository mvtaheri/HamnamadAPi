<?php

namespace App\Providers;

use App\Model\Feed;
use App\Model\User;
use App\Model\Comment;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('base64','App\Validators\Base64Image@validate');

        //***
        Validator::replacer('base64', 'App\Validators\Base64Image@message');
//        Validator::extend('base64', function ($attribute, $value, $parameters, $validator) {
//            if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $value)) {
//                return true;
//            } else {
//                return false;
//            }
//        });
//        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
//
//        });
    }



    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        Relation::morphMap([
            'comment' => Comment::class,
            'feed' => Feed::class,
            'user' =>User::class
        ]);
        $this->app->singleton(\App\Validators\Base64Image::class, function(){
            return new \App\Validators\Base64Image;
        });
    }
}
