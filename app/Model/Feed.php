<?php
/**
 * Created by PhpStorm.
 * User: Mohammad Alipour
 * Date: 7/30/2017
 * Time: 11:21 PM
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Feed
 * @package App\Models
 */
class Feed extends Eloquent
{
    /**
     * @var string
     */

    protected $table = 'feeds';

    protected $guarded = [];

    /**
     *
     */
    public static function getAllUserFeeds()
    {

    }

    /**
     * @param $marketId
     * @return mixed
     */
//    public static function getFeedOfMarket($marketId)
//    {
//        return self::leftJoinWhere(
//            'tag', 'feeds.id', '=', 'tag.feed_id'
//            , ['column' => 'tag.market_id', 'operator' => '=', 'value' => $marketId])->get()->toArray();
//    }

    public function tags()
    {

        return $this->hasMany(Tag::class);

    }

    public function tag()
    {
        return $this->belongsToMany(User::class, 'tag', 'user_id', 'feed_id');
    }


    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

//    public function userLiked()
//    {
//        return $this->belongsToMany(User::class, 'like_feed');
//    }

//    public function like()
//    {
//        return $this->hasMany(LikeFeed::class, 'feed_id');
//    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function findByUserId($userId)
    {
        return self::whereIn('user_id', [$userId]);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
//    public function likes()
//    {
//        return $this->morphToMany(Like::class, 'likeable', 'likeable');
//    }
//
    public function userLike()
    {
        return $this->likes()
            ->where('user_id', Auth::user()->id);
    }

    public function userUnlike()
    {
        return $this->likes()
            ->where('user_id', Auth::user()->id)->onlyTrashed();
    }

}
