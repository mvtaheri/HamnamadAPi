<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/8/2018
 * Time: 9:01 AM
 */

namespace App\Jobs\Crawl;


use App\Contract\Job\Job;
use App\Helpers\Curl;

/**
 * Class Mabna
 * @package App\Jobs\Crawl
 */
class Mabna implements Job
{

    /**
     * @var string
     */
    protected $url = "https://v1.db.api.mabnadp.com/";

    /**
     * @var
     */
    protected $params;

    /**
     * @var array
     */
    protected $header = [
        "Authorization: Basic YmY0MDIyMjljYmFkYzNlMzhhOWZmZmM2MzAwMmE2NDc6",
        "Cache-Control: no-cache",
    ];

    /**
     * Mabna constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        return Curl::Get($this->url, $this->params, $this->header);
    }
}