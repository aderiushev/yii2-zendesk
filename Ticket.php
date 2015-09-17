<?php

namespace hutsi\zendesk;

use Yii;
use yii\base\Model;

/**
 * Class Ticket
 * @author Derushev Aleksey <derushev.alexey@gmail.com>
 * @package hutsi\zendesk
 * https://developer.zendesk.com/rest_api/docs/core/tickets
 */
class Ticket extends Model
{
    public $id;
    public $url;
    public $external_id;
    public $type;
    public $subject;
    public $raw_subject;
    public $description;
    public $priority;
    public $status;
    public $recipient;
    public $requester_id;
    public $submitter_id;
    public $asignee_id;
    public $group_id;
    public $collaborator_ids;
    public $forum_topic_id;
    public $problem_id;
    public $has_incidents;
    public $due_at;
    public $tags;
    public $via;
    public $custom_fields;
    public $satisfaction_rating;
    public $sharing_agreement_ids;
    public $followup_ids;
    public $brand_id;
    public $created_at;
    public $updated_at;

    public $requester;

    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'due_at'], 'safe'],
            ['type', 'default', 'value' => 'question'],
            ['priority', 'defalut', 'value' => 'normal'],
            ['status', 'defalut', 'value' => 'new'],
            [['collaborator_ids', 'tags', 'custom_fields', 'sharing_agreement_ids', 'followup_ids'], function($attribute) {
                return is_array($this->$attribute);
            }],
            [['external_id', 'type', 'subject', 'raw_subject', 'description', 'priority', 'status', 'recipient'], 'string'],
            [['requester_id', 'submitter_id', 'assignee_id', 'organization_id', 'group_id', 'forum_topic_id', 'problem_id', 'ticket_form_id', 'brand_id'], 'integer'],
            ['has_incidents', 'boolean'],
            ['email', 'email'],
            ['url', 'url'],
            ['role', 'default', 'value' => 'end-user'],

        ];
    }

    /**
     * @return mixed
     */
    public function getRequester()
    {
        return Yii::$app->zendesk->get('/users/'.$this->requester_id.'.json');
    }

    /**
     * Performs update or create Ticket
     * @return mixed
     */
    public function save()
    {
        if ($this->isNewRecord) {
            return Yii::$app->zendesk->post('/tickets.json', [
                'ticket' => $this->getAttributes()
            ]);
        }
        else {
            return Yii::$app->zendesk->put('/tickets'.$this->id.'.json', [
                'ticket' => $this->getAttributes()
            ]);
        }
    }
}