<?php

namespace hutsi\zendesk;

use Yii;
use yii\base\Model;

/**
 * Class Attachment
 * @author Derushev Aleksey <derushev.alexey@gmail.com>
 * @package hutsi\zendesk
 * https://developer.zendesk.com/rest_api/docs/core/attachments
 */
class Attachment extends Model
{
    public $id;
    public $file_name;
    public $content_url;
    public $content_type;
    public $size;
    public $thumbnails;
    public $inline;

    public function rules()
    {
        return [
            [['thumbnails'], function($attribute) {
                return is_array($this->$attribute);
            }],
            [['file_name', 'content_url', 'content_type'], 'string'],
            [['size'], 'integer'],
            ['inline', 'boolean'],
            [['thumbnails'], function($attribute) {
                return is_array($this->$attribute);
            }],

        ];
    }

    /**
     * Performs update or create Ticket
     * @return mixed
     */
    public function save()
    {
        if ($this->isNewRecord) {
            return Yii::$app->zendesk->post('/uploads.json', [
                'ticket' => $this->getAttributes()
            ]);
        }
        else {
            return;
        }
    }
}