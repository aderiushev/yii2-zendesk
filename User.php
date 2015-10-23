<?php

namespace hutsi\zendesk;

use Yii;
use yii\base\Model;

/**
 * Class User
 * @author Derushev Aleksey <derushev.alexey@gmail.com>
 * @package hutsi\zendesk
 * https://developer.zendesk.com/rest_api/docs/core/users
 */
class User extends Model
{
    public $id;
    public $url;
    public $name;
    public $created_at;
    public $updated_at;
    public $time_zone;
    public $locale;
    public $locale_id = 8;
    public $organization_id;
    public $role;
    public $verified;
    public $email;
    public $phone;

    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            ['email', 'email'],
            ['email', 'required'],
            ['name', 'default', 'value' => function($model, $attribute) {
                return explode('@', $this->email)[0];
            }, 'skipOnEmpty' => false],
            [['name'], 'required'],
            [['name', 'time_zone', 'locale', 'phone'], 'string'],
            [['locale_id', 'organization_id', 'id'], 'integer'],

            ['url', 'url'],
            ['role', 'default', 'value' => 'end-user'],

        ];
    }

    /**
     * @TODO
     */
    public function getPhoto()
    {
        return;
    }

    /**
     * Performs update or create User
     * @param bool $runValidation
     * @return mixed
     */
    public function save($runValidation = true)
    {
        if ($runValidation) {
            $this->validate();
        }

        if ($this->id) {
            return Yii::$app->zendesk->put('/users/'.$this->id.'.json', [
                'body' => json_encode([
                        'user' => $this->getAttributes()
                    ])
            ]);
        }
        else {
            $result =  Yii::$app->zendesk->post('/users.json', [
                'body' => json_encode([
                    'user' => $this->getAttributes()
                ])
            ]);
            $this->id = $result['user']['id'];

            return $this->id;
        }
    }
}