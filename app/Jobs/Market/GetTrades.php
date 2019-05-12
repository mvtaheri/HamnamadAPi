<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/16/2018
 * Time: 10:16 PM
 */

namespace App\Jobs\Market;


use App\Contract\Job\Job;
use App\Jobs\Crawl\Mabna;
use Carbon\Carbon;

class GetTrades implements Job
{
    private $request;

    /**
     * GetTrades constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     * @throws \App\Exceptions\Job\UnexpectedJobException
     */
    public function handle()
    {
        $utc = 12620;

        $fifteen = Carbon::now()->subMinute(15)->timestamp + $utc;
        $aDay = Carbon::now()->subDay(1)->timestamp + $utc;
        $aWeek = Carbon::now()->subWeek(1)->timestamp + $utc;
        $aMonth = Carbon::now()->subMonth(1)->timestamp + $utc;
        $aYear = Carbon::now()->subYear(1)->timestamp + $utc;


        switch ($this->request['limit']) {
            case "1d":
                $date = jdate()::forge($aDay)->format('%Y%m%dhis');
                break;

            case "1w":
                $date = jdate()::forge($aWeek)->format('%Y%m%dhis');
                break;

            case "1m":
                $date = jdate()::forge($aMonth)->format('%Y%m%dhis');
                break;

            case "1y":
                $date = jdate()::forge($aYear)->format('%Y%m%dhis');
                break;

            default:
            case "15m":
                $date = jdate()::forge($fifteen)->format('%Y%m%dhis');
                break;
        }


        $now = jdate()::forge(time() + $utc)->format('%Y%m%dhis');

        $url = "exchange/trades?"
            . "instrument.id={$this->request['instrument_id']}"
            . "&date_time={$now},{$date}"
            . "&date_time_op=bw";

        return dispatch(new Mabna($url));
    }
}