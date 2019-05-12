<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/25/17
 * Time: 8:47 PM
 */

namespace App\Model;


use App\Components\UserOrder;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;
use mysql_xdevapi\Exception;

/**
 * Class Order
 * @package App\Models
 */
class Order extends Eloquent
{

   protected $connection="mongodb";
    /**
     * @var string
     */
    protected  $collection = "order";

    /**
     * @param $marketId
     * @param $date
     * @return mixed
     */
    public static function today($marketId, $date)
    {
        $result['buying']=self::where('market_id',$marketId)->where('type','buy')->where('date',$date)->count();
        $result['selling']=self::where('market_id',$marketId)->where('type','sell') ->where('date',$date)->count();

        return $result;
    }

    /**
     * @param $marketId
     * @param $date
     */
    public static function week($marketId, $date)
    {
        //TODO return count of buy and sell of market in a week ago
    }

    /**
     * @param $marketId
     * @param $date
     */
    public static function month($marketId, $date)
    {
        //TODO return count of buy and sell of market in a month ago
    }

    /**
     * @param $marketId
     * @param $date
     */
    public static function year($marketId, $date)
    {
        //Todo return count of buy and sell of market in a year ago
    }

    /**
     * @param UserOrder $userOrder
     * @return mixed
     */
    public static function add(UserOrder $userOrder)
    {
        try{
            return self::create([
                'market_id' => $userOrder->getMarketId(),
                'user_id' => $userOrder->getUserId(),
                'price' => $userOrder->getPrice(),
                'count_unit' => $userOrder->getCountOfUnit(),
                'wallet_type' => $userOrder->getWalletType(),
                'type' => $userOrder->getOrderType(),
                'date' => Carbon::now()->toDateString(),
                'created_at' => time()
            ]);
        }catch(\Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $userId
     * @param $walletType
     * @return mixed
     */
    public static function TodaySells($userId, $walletType)
    {
        return self::TodayOrdersBy($userId, 'sell', $walletType);
    }

    /**
     * @param $userId
     * @param $type
     * @param $walletType
     * @return mixed
     */
    public static function TodayOrdersBy($userId, $type, $walletType)
    {
        return self::find(
            [
                'type' => $type,
                'wallet_type' => $walletType,
                'user_id' => $userId,
                'date' => Carbon::createFromTimestamp(time())->toDateString(),
            ]
        );
    }

    public static function TodayOrder($userId ,$walletType){
        return self::find([
            'wallet_type' => $walletType,
             'user_id'     => $userId,
             'date' =>Carbon::now()->toDateString()
        ]);
    }

    /**
     * @param $userId
     * @param $walletType
     * @return mixed
     */
    public static function TodayBuys($userId, $walletType)
    {
        return self::TodayOrdersBy($userId, 'buy', $walletType);
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public static function findByUserId(int $userId)
    {
        return self::find(['user_id'=>$userId]);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function market(){
        return $this->belongsTo(Market::class,'market_id','_id');
    }
}
