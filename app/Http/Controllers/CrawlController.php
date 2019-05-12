<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 1/19/2018
 * Time: 9:21 PM
 */

namespace App\http\Controllers;


use App\Jobs\Crawl\Mabna;
use App\Jobs\Crawl\SyncMarkets;
use App\Jobs\Market\GetTrades;
use App\Models\Market;
use App\Models\MarketCategory;

/**
 * Class CrawlController
 * @package App\Controllers
 */
class CrawlController extends Controller
{
    /**
     * @var mixed
     */
    protected $request;
    /**
     * @var int
     */
    private $time;

    /**
     * CrawlController constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = request();
        $this->time = time() + 16100;
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function mabna()
    {
        $response = $this->dispatch(new Mabna($this->request['url']));

        return $response;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function intradaytrades()
    {

        $date = jdate()::forge($this->time)->format('%Y%m%d000000');


        if ($id = $this->request['instrument_id']) {
            $url = "exchange/intradaytrades?instrument.id={$id}&date_time={$date}&date_time_op=gt&_sort=-date_time";
        } else {
            $url = "exchange/intradaytrades?_count=100&date_time={$date}&date_time_op=gt&_sort=-date_time";
        }

        return $this->dispatch(new Mabna($url));
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function instruments()
    {
        if ($id = $this->request['instrument_id']) {
            $url = "exchange/instruments?id={$id}&_sort=-date_time";
        } else {
            $url = "exchange/instruments?_count=100&_sort=-date_time";
        }

        return $this->dispatch(new Mabna($url));
    }

    /**
     * @throws \Exception
     */
    public function instrumentsSync()
    {
        $this->dispatch(new SyncMarkets());

        return $this->respond('action is success')->success();

    }

    /**
     * @throws \Exception
     */
    public function trades()
    {
        return $this->dispatch(new GetTrades($this->request));
    }

    public function syncCategory()
    {
        $markets = Market::find(['status' => true]);

        foreach ($markets as $market) {
            MarketCategory::insert(['market_id' => (string)$market->_id, 'category_id' => 4]);
        }
    }


}
