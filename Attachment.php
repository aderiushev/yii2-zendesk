<?php

namespace hutsi\zendesk;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\Json;

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
    public $url;

    /**
     * @var $uploadedFile \yii\web\UploadedFile
     */
    public $uploadedFile;

    public function rules()
    {
        return [
            [['file_name', 'content_url', 'content_type'], 'string'],
            [['size', 'id'], 'integer'],
            ['inline', 'boolean'],
            ['url', 'url'],
            ['uploadedFile', function($attribute) {
               return $this->$attribute instanceof \yii\web\UploadedFile;
            }],
            [['thumbnails'], function($attribute) {
                return is_array($this->$attribute);
            }],

        ];
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function save($runValidation = true)
    {
        if ($runValidation) {
            $this->validate();
        }

        if ($this->id) {
            return $this;
        }
        else {
            $file = fopen($this->uploadedFile->tempName, "r");
            $fileSize = $this->uploadedFile->size;
            $postFields = fread($file, $fileSize);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_URL, Yii::$app->zendesk->baseUrl.'/uploads.json?filename=' . $this->uploadedFile->baseName);
            curl_setopt($ch, CURLOPT_USERPWD, Yii::$app->zendesk->user."/token:".Yii::$app->zendesk->apiKey);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/binary']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_INFILE, $file);
            curl_setopt($ch, CURLOPT_INFILESIZE, $fileSize);
            curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            $hasError = curl_errno($ch);
            curl_close($ch);

            if ($hasError) {
                throw new Exception(curl_error($ch));
            }

            $resposeArray = Json::decode($response);

            if (isset($resposeArray['upload']['attachments'])) {
                $attachFields = array_intersect_key($resposeArray['upload']['attachments'][0], $this->getAttributes());
                $this->setAttributes($attachFields);
                return $this->id;
            }
            else {
                return false;
            }
        }
    }
}