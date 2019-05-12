<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/4/2019
 * Time: 2:57 PM
 */

namespace App\Model;

use App\Model\User;
use App\Model\People;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class Watchlist extends Eloquent
{

    protected $connection = 'mongodb';

    protected $collection = 'watchlists';

    protected $guarded = ['_id'];

    public function scopeOwner()
    {
        return self::where('owner_id', Auth::user()->id);
    }

    public function peoples()
    {
        return $this->belongsToMany(People::class);
    }

    public function markets()
    {
        return $this->belongsToMany(Market::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
