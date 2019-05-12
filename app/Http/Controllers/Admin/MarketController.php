<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 12/1/2017
 * Time: 5:57 AM
 */

namespace App\Controllers\Admin;


use App\Controllers\Controller;
use App\Helpers\JWT;
use App\Jobs\Market\GetMarket;
use App\Models\Category;
use App\Models\Market;
use App\Models\MarketCategory;
use App\Models\Parents;
use App\Requests\Admin\Market\AddMarketRequest;
use App\Requests\Admin\Market\DeleteMarket;
use App\Requests\Admin\Market\UpdateMarketRequest;
use App\Requests\Market\GetMarketRequest;
use App\Traits\Admin\AdminValidate;
use App\Transformers\Admin\Market\MarketTransformer;
use App\Transformers\Market\LatestTransformer;
use MongoDB\BSON\ObjectId;

/**
 * Class MarketController
 * @package App\Controllers\Admin
 */
class MarketController extends Controller
{
    use AdminValidate;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var array
     */
    protected $request;

    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        $this->userId = (int)JWT::parse(request('token'))->user_id;
        $this->AdminUserValidation($this->userId);
        $this->request = request();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getMarket()
    {
        GetMarketRequest::validate();
        $market = (array)$this->dispatch(new GetMarket($this->userId, $this->request));
        $marketOfCategory = MarketCategory::whereIn('market_id', [$market['_id']]);
        $categories = (array)Category::findAll()->all();

        foreach ($categories as $key => $category) {
            if ($category->id == $marketOfCategory[0]->category_id)
                $categories[$key]->selected = true;
        }
        $market['categories'] = $categories;

        return $this->respond(MarketTransformer::transform($market))->success();

    }


    /**
     * @return string
     */
    public function getLatestMarket()
    {
        $markets = $this->addChangePriceAndSentimentToMarket();

        return $this->respond(LatestTransformer::transformArray($markets))->success();
    }

    /**
     * @return mixed
     */
    private function addChangePriceAndSentimentToMarket()
    {
        $markets = Market::find(['status' => true]);
        foreach ($markets as $key => $market) {
            $category = Category::getCategoryOfMarket((string)$market->_id);
            $markets[$key]->parent = Parents::getParentOfCategory($category->id);
            $markets[$key]->category = $category;
            $markets[$key]->change = ($market->sell->price - $market->sell->last_price) * 0.01;
            $markets[$key]->sentiment = Market::calculateSentimentToday($market->_id);
        }

        return $markets;
    }

    /**
     * @return string
     */
    public function addMarket()
    {
        AddMarketRequest::validate();
        if ($market = Market::add($this->request)) {
            MarketCategory::insert(['market_id' => $market, 'category_id' => $this->request['category_id']]);

            return $this->respond("Market with id :{$market} inserted ")->success();
        }

        return $this->respond('insert action is failed')->fail();

    }

    /**
     * @return string
     */
    public function updateMarket()
    {
        UpdateMarketRequest::validate();

        $market = Market::update($this->request);
        $marketCategory = MarketCategory::updateOne(
            'market_id',
            $this->request['id'],
            ['category_id' => $this->request['category_id']]
        );
        if ($market > 0 || $marketCategory > 0)
            return $this->respond("Market with id :{$this->request['id']} updated ")->success();

        return $this->respond('update action is failed')->fail();

    }

    /**
     * @return string
     */
    public function removeMarket()
    {
        DeleteMarket::validate();
        $market = Market::deleteOne(['_id' => new ObjectId($this->request['id'])]);
        if ($market->getDeletedCount() > 0)
            return $this->respond("Market with id :{$this->request['id']} deleted ")->success();

        return $this->respond('delete action is failed')->fail();


    }
}