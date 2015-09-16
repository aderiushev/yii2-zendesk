<?php

namespace hutsi\zendesk;

use Yii;
use yii\base\Component;

/**
 * Class Client
 * @author Derushev Aleksey <derushev.alexey@gmail.com>
 * @package hutsi\zendesk
 * based on manual: https://support.zendesk.com/hc/en-us/articles/203691216
 */
class Client extends Component
{
    public $apiKey;
    public $user;
    public $baseUrl;

    /**
     * @var $httpClient \GuzzleHttp\Client
     */
    public $httpClient;

    /**
     * @return \GuzzleHttp\Client
     */
    public function init()
    {
        if (!$this->httpClient) {
            $this->httpClient = new \GuzzleHttp\Client($this->$baseUrl);
        }
    }

    public function execute($method, $requestUrl, $data)
    {
        $request = $this->httpClient->createRequest($method, $this->baseUrl . $requestUrl, [
            'curl' => [
                'CURLOPT_FOLLOWLOCATION' => true,
                'CURLOPT_MAXREDIRS' => 10,
                'CURLOPT_URL' => $this->baseUrl . $requestUrl,
                'CURLOPT_USERPWD' => $this->user . '/token:' . $this->apiKey,
                'CURLOPT_CUSTOMREQUEST' => $method,
                'CURLOPT_POSTFIELDS' => $data,
                'CURLOPT_HTTPHEADER' => ['Content-type: application/json'],
                'CURLOPT_USERAGENT' => 'MozillaXYZ/1.0',
                'CURLOPT_RETURNTRANSFER' => true,
                'CURLOPT_TIMEOUT' => 10
            ]
        ]);

        $r =  $this->httpClient->send($request);
        exit(var_dump($r));
    }

    /*
    function curlWrap($url, $json, $action)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_URL, ZDURL . $url);
        curl_setopt($ch, CURLOPT_USERPWD, ZDUSER . "/token:" . ZDAPIKEY);
        switch ($action) {
            case "POST":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "GET":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                break;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($output);
        return $decoded;
    }
    */
}