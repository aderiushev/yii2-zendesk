<?php

namespace hutsi\zendesk;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class Search
 * @author Derushev Aleksey <derushev.alexey@gmail.com>
 * @package hutsi\zendesk
 * https://developer.zendesk.com/rest_api/docs/core/search
 */
class Search extends Model
{
    const TYPE_TICKET = 'ticket';
    const TYPE_USER = 'user';
    const TYPE_ORGANIZATION = 'organization';
    const TYPE_GROUP = 'group';
    const TYPE_TOPIC = 'topic';

    public $query;
    public $type;
    public $sort_by;
    public $sort_order;

    public function rules()
    {
        return [
            [['type'], 'in', 'range' => [self::TYPE_TICKET, self::TYPE_USER, self::TYPE_ORGANIZATION, self::TYPE_GROUP, self::TYPE_TOPIC]],
            [['query'], function($attribute) {
                return is_array($this->$attribute);
            }],
            [['sort_by'], 'string'],
            [['sort_order'], 'in', 'range' => [SORT_ASC, SORT_DESC]],
        ];
    }

    public function find()
    {
        if ($this->validate()) {
            $httpQuery = http_build_query(ArrayHelper::merge($this->query, $this->getAttributes(['type', 'sort_by', 'sort_order'])));
            $zendeskQuery = strtr($httpQuery, ['=' => ':', '&' => ' ']);
            $response = Yii::$app->zendesk->get('/search.json', ['query' => urldecode($zendeskQuery)]);
            return isset($response['results']) ? $response['results'] : [];
            
        }
        else {
            return false;
        }
    }

    /**
     * Searches for the user
     * @return mixed
     */
    public function users()
    {
        $this->setAttributes(ArrayHelper::merge($this->getAttributes(), ['type' => 'user']));

        $zUsers = [];
        if ($results = $this->find()) {
            foreach ($results as $userData) {
                $user = new User();
                $userFields = array_intersect_key($userData, $user->getAttributes());
                $user->setAttributes($userFields);
                $zUsers[] = $user;
            }
        }

        return $zUsers;
    }
}