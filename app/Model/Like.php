<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/7/2019
 * Time: 12:42 PM
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Like extends Eloquent
{ use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'like';
    protected $guarded = ['id'];

    public function __construct()
    {
        parent::__construct();
    }

    public function likeable()
    {
        return $this->morphTo();
    }

//    public function comments()
//    {
//        return $this->morphedByMany(Comment::class, 'likeable','likeable');
//    }
//
//    public function feeds()
//    {
//        return $this->morphedByMany(Feed::class, 'likeable','likeable');
//    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
