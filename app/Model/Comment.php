<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/11/2018
 * Time: 7:14 AM
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Auth;

class Comment extends Eloquent
{

    protected $table = 'comment';

    protected $guarded = [];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function userLike()
    {
        return $this->likes()->where('user_id', Auth::user()->id);
    }

    public function userUnlike()
    {
        return $this->likes()
            ->where('user_id', Auth::user()->id)->onlyTrashed();
    }
//    public function user()
//    {
//        return $this->belongsTo(User::class);
//    }
//    public function likes(){
//        return $this->morphToMany(Like::class,'likeable','likeable');
//    }
}
