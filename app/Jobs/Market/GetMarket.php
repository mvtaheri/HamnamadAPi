<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/19/17
 * Time: 11:40 PM
 */

namespace App\Jobs\Market;


use App\Contract\Job\Job;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Market;
use App\Models\MarketCategory;
use App\Models\Order;
use App\Models\Parents;
use App\Models\User;
use App\Models\Wallet;
use App\Transformers\Market\MarketTransformer;
use App\Transformers\User\UserProfileTransformer;
use MongoDB\BSON\ObjectID;

/**
 * Class GetMarket
 * @package App\Jobs\Market
 */
class GetMarket implements Job
{

    /**
     * @var
     */
    protected $request;
    /**
     * @var
     */
    protected $userId;

    /**
     * GetMarket constructor.
     * @param $userId
     * @param $request
     */
    public function __construct($userId, $request)
    {
        $this->userId = $userId;
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        // part of Project last
        $market=Market::find(['_id'=>new ObjectId($this->request['id']), 'status'=>true]);
        if (!$market = (array)Market::findOne(['_id' => new ObjectID($this->request['id'])])) {
            return false;
        }
        $otherMarket=$likeMarkets=$users=[];
        $market['alert'] = Alert::has($this->userId, $this->request['id']);
        $market['change'] = ($market['sell']->price - $market['sell']->last_price) * 0.01;
        $market['sentiment'] = Market::calculateSentimentToday($market['_id']);
        $category = (array)Category::getCategoryOfMarket($this->request['id']);
        $transaction = (array)Order::count(['market_id' => new ObjectID($this->request['id'])]);
        if (!is_null($category) && count($category)>0)
            $likeMarkets = MarketCategory::where('category_id', '=', (int)$category[0]->category_id);
        $markets = array_column($likeMarkets, 'market_id');
        foreach ($markets as $m) {
            if ($m !== $this->request['id']) {
                $otherMarket[] = new ObjectId($m);
            }
        }
        if (sizeof($otherMarket)>0){
         $categoryMarket =  Market::find(['_id' => ['$in' => $otherMarket]], ['limit' => 3]);
        $market['like_markets'] = MarketTransformer::transformArray($categoryMarket);
        }else
            $market['like_markets']=[];


        //TODO this item must get information from mapna
        $market['max_price'] = $market['buy']['last_price'];
        $wallet = Wallet::find(
            ['market_id' => new ObjectID($this->request['id'])],
            ['sort' => ['total' => -1], 'limit' => 3]
        );

        foreach ($wallet as $user) {
            $users[] = (array)User::findById((int)$user->user_id);
        }
        (count($users)>0)? $market['people']=UserProfileTransformer::transformArray($users) :$market['people']=[];
        $market['people'] = UserProfileTransformer::transformArray($users);
        ( count($category)>0 ) ? $market['parent'] = (array)Parents::getParentOfCategory((int)$category[0]->category_id):$market['parent']=[];

        return array_merge($market, $category, $transaction);
    }
}
