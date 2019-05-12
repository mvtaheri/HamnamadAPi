<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 4/15/2018
 * Time: 8:41 PM
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class LikeFeed
 * @package App\Models
 */
class LikeFeed extends Eloquent
{
    /**
     * @var string
     */
    protected $table ="like_feed";

    /**
     * @param $userId
     * @param $feedId
     * @return mixed
     */
    public static function like(int $userId, int $feedId)
    {
        return self::insert(['user_id' => (int)$userId, 'feed_id' => (int)$feedId]);
    }


    /**
     * @param $id
     * @return int
     */
    public static function deslike($id)
    {
        return self::deleteById($id);
    }

    /**
     * @param int $userId
     * @param int $feedId
     * @return mixed
     */
    public static function checkUserLikeFeed(int $userId, int $feedId)
    {
        return self::where('user_id',$userId)->where('feed_id',$feedId)->first() ? true :false;
    }

    /**
     * @param int $feedId
     * @return int
     */
    public static function countnumberOfFeedLike(int $feedId)
    {
        return self::where('feed_id', '=', $feedId)->count();
    }

    /**
     * curent user Like Spesific Feed Or NOt
     * @param $userId
     * @param $feedId
     * @return mixed
     */
    public static function LikedByCurentUser($userId, $feedId)
    {
        return self::where('user_id',$userId)->where('feed_id',$feedId)->count() > 0 ? true :false ;
    }

    public function feed(){
        return $this->belongsTo(Feed::class,'id','feed_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'id','user_id');
    }

}
