<?php

namespace hutsi\zendesk;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

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
    public $password;
    public $authType;

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
            $client = new \GuzzleHttp\Client([
                'base_url' => $this->baseUrl,
                'defaults' => [
                    'verify' => false,
                    'auth'  => $this->getAuthSettings(),
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ]
            ]);

            $this->httpClient = new \understeam\httpclient\Client([
                'client' => $client
            ]);
        }
    }

    /**
     * @TODO: Oauth support
     * @return array
     */
    public function getAuthSettings()
    {
        switch ($this->authType) {
            case 'basic':
                return [$this->user, $this->password, $this->authType];
                break;
            case 'digest':
                return [$this->user . '/token', $this->apiKey, $this->authType];
                break;
            default:
                $result = [];
                break;
        }

        return $result;
    }

    /**
     * @param $method
     * @param $requestUrl
     * @param array $options
     * @return bool
     */
    public function execute($method, $requestUrl, $options = [])
    {

        try {
            return $this->httpClient->request($this->baseUrl . $requestUrl, $method, null, $options);
        }
        catch(\Exception $e) {
            return false;
        }

    }

    public function beforeRequest()
    {

    }

    /**
     * @param $requestUrl
     * @param array $options
     * @return bool
     */
    public function get($requestUrl, $options = [])
    {
        return $this->execute('GET', $requestUrl, $options);
    }

    /**
     * @param $requestUrl
     * @param array $options
     * @return bool
     */
    public function post($requestUrl, $options = [])
    {
        return $this->execute('POST', $requestUrl, $options);
    }

    /**
     * @param $requestUrl
     * @param array $options
     * @return bool
     */
    public function put($requestUrl, $options = [])
    {
        return $this->execute('PUT', $requestUrl, $options);
    }

    /**
     * @param $requestUrl
     * @param array $options
     * @return mixed
     */
    public function delete($requestUrl, $options = [])
    {
        return $this->execute('DELETE', $requestUrl, $options);
    }
}