<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/7/17
 * Time: 12:52 AM
 */

namespace App\Model;


use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;

/**
 * Class Market
 * @package App\Models
 */
class Market extends Eloquent
{
    protected $connection='mongodb';
    /**
     * @var string
     */
    protected  $collection = 'market';

    protected $guarded =['_id'];


    /**
     * @var object
     */
    protected $buy;

    /**
     * @var int
     */
    protected $buy_last_price;

    /**
     * @var int
     */
    protected $buy_price;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var object
     */
    protected $sell;

    /**
     * @var int
     */
    protected $sell_last_price;

    /**
     * @var int
     */
    protected $sell_price;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $status;
    protected $alert;
    protected $change;
    protected $sentiment;


    /**
     * @param $marketId
     * @return mixed
     */
    public static function calculateSentimentToday($marketId)
    {
        $date = Carbon::createFromTimestamp(time())->toDateString();
        $todayTransaction = Order::today($marketId, $date);
        $sumTodayTransaction = ($todayTransaction['buying'] + $todayTransaction['selling']);
        $sumTodayTransaction = ($sumTodayTransaction == 0) ? 1 : $sumTodayTransaction;
        $buying = ($todayTransaction['buying'] * 100) / (int)$sumTodayTransaction;
        $selling = ($todayTransaction['selling'] * 100) / (int)$sumTodayTransaction;

        if ($buying > $selling) {
            $result['change'] = (int)$buying ?? 0;
            $result['type'] = 'buying';
        }if ($selling > $buying){
        $result['change'] = (int)$selling ?? 0;
        $result['type'] = 'selling';
           }
        else {
            $result['change'] = 0;
            $result['type'] = 'equal';
        }

        return $result;
    }

    /**
     * @param $request
     * @return mixed
     */
    public  function addmarket($request)
    {
        $market = self::create([
            'title' => $request['title'],
            'special' => ($request['special'] == 'true') ? true : false,
            'buy' => [
                'price' => (int)$request['buy_price'],
                'last_price' => (int)$request['buy_price']
            ],
            'sell' => [
                'price' => (int)$request['sell_price'],
                'last_price' => (int)$request['sell_price']
            ],
            'status' => ($request['status'] == 'true') ? true : false
        ]);

        return $market->getInsertedId();
    }

    /**
     * @param $request
     * @return mixed
     */
    public  function updatemarket($request)
    {
        $market = self::updateOne(
            ['_id' => new ObjectId($request['id'])],
            [
                'title' => $request['title'],
                'buy.price' => (int)$request['buy_price'],
                'buy.last_price' => (int)$request['buy_price'],
                'sell.price' => (int)$request['sell_price'],
                'sell.last_price' => (int)$request['sell_price'],
                'special' => ($request['special'] == 'true') ? true : false,
                'status' => ($request['status'] == 'true') ? true : false
            ], []
        );

        return $market->getModifiedCount();
    }


    public static function generate($title, $englishTitle, $mabnaId, int $price)
    {
        return self::insertOne([
            "title" => $title,
            "english_title" => $englishTitle,
            "buy" => [
                "last_price" => 0,
                "price" => 0
            ],
            "image" => "",
            "sell" => [
                "last_price" => 0,
                "price" => $price
            ],
            'mabna_id' => $mabnaId,
            "status" => true
        ]);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function order(){
        return $this->hasMany(Order::class,'market_id');
    }

    public function alerts(){
        return $this->hasMany(Alert::class,'market_id');
    }

    /**
     * return Alert for this market that Coresponding with Curent User Login
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function currentUserAlert(){
        return $this->hasMany(Alert::class,'market_id')
            ->where('user_id',Auth::user()->id);
    }
    public function wallet(){
        return $this->hasMany(Wallet::class,'market_id','_id');
    }

}
