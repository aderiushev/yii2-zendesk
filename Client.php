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
            $this->httpClient = new \GuzzleHttp\Client([
                'base_url' => $this->baseUrl,
                'defaults' => [
                    'verify' => false,
                    'auth'  => $this->getAuthSettings(),
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ]
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
     * @param array $data
     * @return mixed
     */
    public function execute($method, $requestUrl, $data = [])
    {
        $request = $this->httpClient->createRequest($method, $this->baseUrl . $requestUrl);
        $request->setQuery($data);

        $response = $this->httpClient->send($request);
        return $response->json();
    }

    /**
     * @param $requestUrl
     * @param array $data
     * @return mixed
     */
    public function get($requestUrl, $data = [])
    {
        return $this->execute('GET', $requestUrl, $data);
    }

    /**
     * @param $requestUrl
     * @param array $data
     * @return mixed
     */
    public function post($requestUrl, $data = [])
    {
        return $this->execute('POST', $requestUrl, $data);
    }

    /**
     * @param $requestUrl
     * @param array $data
     * @return mixed
     */
    public function put($requestUrl, $data = [])
    {
        return $this->execute('PUT', $requestUrl, $data);
    }

    /**
     * @param $requestUrl
     * @param array $data
     * @return mixed
     */
    public function delete($requestUrl, $data = [])
    {
        return $this->execute('DELETE', $requestUrl, $data);
    }

    public function search()
    {
        return $this->get('search.json', []);
    }
}