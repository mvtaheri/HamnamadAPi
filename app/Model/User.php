<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


class User extends Eloquent implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, HasApiTokens, CanResetPassword, Notifiable, HybridRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password', 'email', 'name', 'type', 'gender', 'birthday'
        , 'country', 'address', 'know_level', 'experience_level', 'avatar', 'setting'];
    /**
     * @var string
     */
    protected $connection = 'mysql';
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Find the user instance for the given username.
     *
     * @param  string $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return self::where('username', $username)->first();
    }

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }


    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeActiveUser($query)
    {
        return $query->where('enabled', true);
    }


    public static function getUserByEmail($email)
    {
        return self::findOne('email', $email);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

//    public function likedFeed()
//    {
//        return $this->belongsToMany(Feed::class, 'like_feed');
//    }

    public function feeds()
    {
        return $this->hasMany(Feed::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function people()
    {
        return $this->hasOne(People::class, 'user_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function sellOrder()
    {
        return $this->order()
            ->where('type', 'sell');
    }

    public function buyOrder()
    {
        return $this->order()
            ->where('type', 'buy');
    }

    public function alert()
    {
        return $this->hasMany(Alert::class, 'user_id');
    }

    public function wallet()
    {
        return $this->hasMany(Wallet::class, 'user_id');
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class, 'owner_id');
    }

    public function defaultWatchlist()
    {
        return $this->watchlists()
            ->where('default', true);
    }

    public function checkoutRequests()
    {
        return $this->hasMany(CheckoutRequest::class, 'user_id');
    }
    public function like(){
        return $this->hasMany(Like::class,'user_id');
    }
}
