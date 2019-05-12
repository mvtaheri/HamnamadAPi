<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/23/2018
 * Time: 12:25 AM
 */

namespace App\Http\Controllers;


use App\Helpers\JWT;
use App\Jobs\Efficiency\CategoryEfficiency;
use App\Jobs\Efficiency\MarketEfficiency;
use App\Jobs\Efficiency\UserEfficiency;
use App\Jobs\Efficiency\UserEfficiencySync;
use App\Jobs\Efficiency\UserRisk;
use App\Jobs\Efficiency\UserRiskDetail;
use App\Jobs\Efficiency\UsersEfficiency;
use App\Jobs\Efficiency\UserTrades;
use App\Models\Category;
use App\Models\DailyEfficiency;
use App\Models\Market;
use App\Requests\Efficiency\UserEfficiencyRequest;
use App\Transformers\Efficiency\CategoryEfficiencyTransformer;
use App\Transformers\Efficiency\MarketEfficiencyTransformer;
use App\Transformers\Efficiency\UserEfficiencyTransformer;
use Carbon\Carbon;
use Psr\Container\ContainerInterface;

/**
 * Class EfficiencyController
 * @package App\Controllers
 */
class EfficiencyController extends Controller
{

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var mixed
     */
    protected $request;
    protected $container;

    /**
     * EfficiencyController constructor.
     * @param $userId
     */
    public function __construct(ContainerInterface $container )
    {
        $this->container=$container;
        $token=$container['request']->getHeader('Authorization');
        $this->request = request();
        $this->userId = (int)JWT::parse($token[0])->user_id;
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function marketEfficiency()
    {
        $this->userId = $this->request['user_id'] ?? $this->userId;
        $efficienciesMarket = $this->dispatch(new MarketEfficiency($this->userId));
        foreach ($efficienciesMarket as $key => $efficiency) {
            $efficienciesMarket[$key]['market'] = Market::findById($efficiency['data']);
        }

        return $this->respond(MarketEfficiencyTransformer::transformArray($efficienciesMarket))->success();
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function categoryEfficiency()
    {
        $this->userId = $this->request['user_id'] ?? $this->userId;
        $efficienciesMarket = $this->dispatch(new CategoryEfficiency($this->userId));
        foreach ($efficienciesMarket as $key => $efficiency) {
            $efficienciesMarket[$key]['category'] = (Category::findById((int)$efficiency['data'])) ?? [] ;
        }

        return $this->respond(CategoryEfficiencyTransformer::transformArray($efficienciesMarket))->success();
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function usersEfficiency()
    {
        $this->dispatch(new UsersEfficiency($this->container));

        return $this->respond()->success();
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function userTrades()
    {
        $this->userId = $this->request['user_id'] ?? $this->userId;

        $result = $this->dispatch(new UserTrades($this->userId, $this->request));

        return $this->respond($result)->success();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function userRisk()
    {
        $this->userId = $this->request['user_id'] ?? $this->userId;

        $result = $this->dispatch(new UserRisk($this->container));

        return $this->respond($result)->success();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function userRiskDetail()
    {
        UserEfficiencyRequest::validate();

        $this->userId = $this->request['user_id'] ?? $this->userId;

        $result = $this->dispatch(new UserRiskDetail($this->userId, $this->request));

        return $this->respond($result)->success();
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function userEfficiencySync()
    {
        $yearlyEfficiency = $this->dispatch(new UserEfficiencySync($this->request));

        return $this->respond(UserEfficiencyTransformer::transformArray($yearlyEfficiency))->success();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function userEfficiency()
    {
        $this->userId = $this->request['user_id'] ?? $this->userId;
        $yearlyEfficiency = $this->dispatch(new UserEfficiency($this->userId, $this->request));

        return $this->respond(UserEfficiencyTransformer::transformArray($yearlyEfficiency))->success();
    }

    /**
     * @return int
     */
    private function CalculateUserEfficiencyByLimitation()
    {
        UserEfficiencyRequest::validate();

        $limit = (int)$this->request['limit'];

        $limitTimestamp = Carbon::now()->subDays($limit)->timestamp;
        $this->checkDateLimitationInserted($limitTimestamp, $limit);
        $walletType = $this->request['wallet_type'];
        $efficiencies = DailyEfficiency::getByDateLimited($this->userId, $walletType, $limitTimestamp);


        return $this->calculateLimitationEfficiency($efficiencies);
    }

    /**
     * @param $limitTimestamp
     * @param $limit
     * @return mixed
     */
    private function checkDateLimitationInserted($limitTimestamp, $limit)
    {
        $daily = DailyEfficiency::findOne([
            'user_id'     => $this->userId,
            'wallet_type' => $this->request['wallet_type'],
        ], ['sort' => ['created_at' => 1]]);

        $limitDate = Carbon::now()->subDays($limit)->day;
        $lastEfficiencyInserted = Carbon::createFromTimestamp($daily->created_at)->diffInDays();

        if ($daily->created_at > $limitTimestamp && $daily->date->day !== $limitDate) {
            die($this
                ->respond("Last efficiency inserted is {$lastEfficiencyInserted} days ago")
                ->fail('Date limitation is more than efficiencies inserted')
            );
        }

        return $daily;
    }
}
