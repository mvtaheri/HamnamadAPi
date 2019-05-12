<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/8/17
 * Time: 12:04 AM
 */

namespace App\Http\Controllers;

use App\Model\Feed;
use App\Model\Alert;
use App\Model\Market;
use App\Model\Parents;
use App\Model\Category;
use App\Helpers\FileContent;
use App\Jobs\Market\SetAlert;
use App\Jobs\Market\GetAlert;
use App\Jobs\Market\GetMarket;
use App\Model\MarketCategory;
use App\Jobs\Market\UpdateAlert;
use App\Jobs\Market\RemoveAlert;
use App\Jobs\Market\GetWatchList;
use App\Jobs\Market\UserWatchList;
use App\Model\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;
use Psr\Container\ContainerInterface;
use App\Requests\Market\TradesRequest;
use App\Jobs\Market\AddToUserWatchList;
use App\Requests\Market\GetAlertRequest;
use App\Requests\Market\SetAlertRequest;
use App\Jobs\Market\RemoveWatchlistItem;
use App\Helpers\Parents as ParentsHelper;
use App\Requests\Market\GetMarketRequest;
use App\Requests\Market\RemoveAlertRequest;
use App\Requests\Market\UpdateAlertRequest;
use App\Requests\Market\AddWatchListRequest;
use App\Transformers\Market\LatestTransformer;
use App\Transformers\Market\MarketTransformer;
use App\Transformers\Market\TradesTransformer;
use App\Requests\Market\RemoveWatchlistItemRequest;

/**
 * Class MarketController
 * @package App\Controllers
 */
class MarketController extends Controller
{


    /**
     * @var
     */
    protected $userId;

    /**
     * @var mixed
     */
    protected $request;

    /**
     * MarketController constructor.
     */
    public function __construct()
    {
        $this->userId = Auth::user()->id;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getMarket(Request $request)
    {
        $this->validate($request, [
            'market_id' => 'required|exists:mongodb.market,_id',
        ]);
        $market = Market::find($request->input('market_id'));
        if ($market) {
            $collect['market'] = $market;
            $collect['alert'] = $market->curentUserAlert()->get();
            $collect['change'] = ($market->sell['price'] - $market->sell['last_price']) * 0.01;
            $collect['sentiment'] = Market::calculateSentimentToday($market->_id);
            $collect['category'] = $market->category()->get();
            $collect['transaction'] = $market->order->count();
            $likeMarkets = Category::where('id', $market->category_id)->with(['market' => function ($query) {
                $query->take(15);
            }])->get();
//            $wallets=Wallet::where('market_id',$market->_id)->with(['market','user'])->get();
//           $walletsssssss=$market->with(['wallet'])->get();
            return response()->json(['status' => true, 'market' => $collect, 'likeMarket' => $likeMarkets]);
        }
        return response()->json(['status' => false, 'message' => 'Market not found !'], 400);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function addFeedToFieldsOfMarket()
    {
        $market = $this->dispatch(new GetMarket($this->userId, $this->request));
        $market['feeds'] = (array)Feed::getFeedOfMarket($this->request['id']);

        return $market;
    }

    /**
     * @return string
     */
    public function getLatestMarket(Request $request)
    {
        $limit = ($request->has('limit')) ? (int)$request->input('limit') : 20;
        $markets = Market::paginate($limit);
        $markets = $this->addChangePriceAndSentimentToMarket($markets);
        return response()->json(['status' => true, 'data' => $markets], 200);
    }

    /**
     * @param $markets
     * @return mixed
     */
    private function addChangePriceAndSentimentToMarket($markets)
    {
        $user = Auth::user();
        $extraInfo = [];
        foreach ($markets as $key => $market) {
            $markets[$key]->category = $market->category()->get();
            $markets[$key]->userAlert = $market->currentUserAlert()->get();
            $markets[$key]->change = ($market->sell['price'] - $market->sell['last_price']) * 0.01;
            $markets[$key]->sentiment = Market::calculateSentimentToday($market->_id);
        }

        return $markets;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function setMarketAlert(Request $request)
    {
        $this->validate($request,
            [
                'market_id' => 'required|exists:mongodb.market,_id',
                'rate' => 'required|numeric'
            ]);
        try {
            $market = Market::find($request->input('market_id'));
            $alert = $market->alerts()->create([
                'user_id' => Auth::user()->id,
                'rate' => (int)$request->input('rate')
            ]);
            return response()->json(['status' => true, 'message' => 'alert set Successfully !']);
        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getMarketAlert(Request $request)
    {
        $this->validate($request, [
            'market_id' => 'required|exists:mongodb.market,_id',
        ]);
        try {
            $market = Market::find($request->input('market_id'));
            $alert = $market->currentUserAlert()->get();
            return response()->json(['status' => true, 'data' => $alert], 200);
        } catch (\Exception $exception) {
            return response()->json(['data' => false, 'message' => $exception->getMessage()], 400);
        }

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function updateMarketAlert(Request $request)
    {
        $this->validate($request, [
            'market_id' => 'required|exists:mongodb.market,_id',
            'rate' => 'required|numeric'
        ]);
        try {
            $market = Market::find($request->input('market_id'));
            $alert = $market->currentUserAlert()->update([
                'rate' => $request->input('rate')
            ]);
            return response()->json(['status' => true, 'message' => 'update alert successfully!'], 400);
        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function removeMarketAlert(Request $request)
    {
        $this->validate($request, [
            'market_id' => 'required|exists:mongodb.market,_id'
        ]);
        try {
            $market = Market::find($request->input('market_id'));
            $market->currentUserAlert()->delete();
            return response()->json(['status' => true, 'message' => 'delete alert successfully'], 400);
        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function addWatchList(Request $request)
    {

        AddWatchListRequest::validate();
        $this->dispatch(new UserWatchList($this->userId,
                $this->request,
                'add',
                'markets'
            )
        );

        return $this->respond('')->success();

    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getWatchList()
    {
        $markets = $this->dispatch(new GetWatchList($this->userId, 'markets'));

        return $this->respond($markets)->success();

    }

    /**
     * @return string
     * @throws \Exception
     */
    public function removeWatchlist()
    {
        RemoveAlertRequest::validate();
        $this->dispatch(new UserWatchList(
            $this->userId, $this->request,
            'remove',
            'markets'
        ));

        return $this->respond('')->success('Item removed From List !');

    }

    /**
     * @throws \Exception
     */
    public function trades(Request $request)
    {
        $this->validate($request, [
            'instrument_id' => 'required',
            'limit' => 'required'
        ]);
        $url = 'http://185.173.106.244/api/v1/';
        $item = 'exchange/trades';
        $params = [
            'instrument_id' => $request->input('instrument_id'),
            'limit' => $request->input('limit')
        ];

//        $trades = FileContent::get($url, $item, $params);

        $random = rand(2000, 4000);

        switch ($request->input('limit')) {
            case "1d":
                $data = $this->generateData($random, rand(2, 8));
                break;

            case "1w":
                $data = $this->generateData($random, rand(15, 25));

                break;

            case "1m":
                $data = $this->generateData($random, rand(27, 40));
                break;

            case "1y":
                $data = $this->generateData($random, rand(140, 250));
                break;

            default:
            case "15m":
                $data = $this->generateData($random, rand(1, 3));
                break;
        }

        $prices = array_column($data, 'close_price');
        $result['detail'] = $data;
        $result['change'] = [
            'percent' => ($prices[0] - last($prices)) * 0.1,
            'value' => $prices[0] - last($prices)
        ];

        return response()->json(['status' => true, 'data' => $result], 200);

    }

    /**
     * @param $random
     * @param $count
     * @return array
     */
    private function generateData($random, $count): array
    {
        for ($x = 0; $x <= $count; $x++) {
            $data[] = [
                'date_time' => '13930714122956',
                'open_price' => $random,
                "high_price" => $random + rand(10, 100),
                "low_price" => $random - rand(10, 100),
                "close_price" => $random + rand(50, 100),
                "volume" => rand(10000, 100000),
                "split" => "",
                "close_price_change" => rand(10, 100)
            ];
        }
        return $data;
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function discoverMarket()
    {
        try {
            $markets = Market::where('special', true)->with('category')->paginate(20, ['_id', 'title']);
            return response()->json(['status' => true, 'data' => $markets], 400);
        } catch (\Exception $ex) {
            return response()->json(['status' => false, 'message' => $ex->getMessage()], 400);
        }
    }

    /**
     * @param $marketId
     * @return array
     */
    private function addCategoryToMarket($marketId)
    {
        $marketOfCategory = MarketCategory::whereIn('market_id', [$marketId]);

        return Category::getCategoryOfMarket($marketId);
    }

}
