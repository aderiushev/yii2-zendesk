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
    const PRIORITY_URGENT = 'urgent';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_LOW = 'low';

    const STATUS_NEW = 'new';
    const STATUS_OPEN = 'open';
    const STATUS_PENDING = 'pending';
    const STATUS_HOLD = 'hold';
    const STATUS_SOLVED = 'solved';
    const STATUS_CLOSED = 'closed';

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
    public $assignee_id;
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
    public $comment;

    public $requester;

    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'due_at'], 'safe'],
            ['type', 'default', 'value' => 'question'],
            ['status', 'default', 'value' => 'new'],
            [['collaborator_ids', 'tags', 'custom_fields', 'sharing_agreement_ids', 'followup_ids', 'comment'], function($attribute) {
                return is_array($this->$attribute);
            }],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_NEW, self::STATUS_OPEN, self::STATUS_PENDING, self::STATUS_HOLD, self::STATUS_SOLVED, self::STATUS_CLOSED]],
            ['priority', 'default', 'value' => self::PRIORITY_NORMAL],
            ['priority', 'in', 'range' => [self::PRIORITY_URGENT, self::PRIORITY_HIGH, self::PRIORITY_NORMAL, self::PRIORITY_LOW]],
            [['external_id', 'type', 'subject', 'raw_subject', 'description', 'priority', 'status', 'recipient'], 'string'],
            [['requester_id', 'submitter_id', 'assignee_id', 'group_id', 'forum_topic_id', 'problem_id', 'brand_id'], 'integer'],
            ['has_incidents', 'boolean'],
            ['url', 'url'],
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
     * @param bool $runValidation
     * @return mixed
     */
    public function save($runValidation = true)
    {
        if ($runValidation) {
            $this->validate();
        }

        if ($this->id) {
            return Yii::$app->zendesk->put('/tickets/'.$this->id.'.json', [
                'body' => json_encode([
                    'ticket' => $this->getAttributes()
                ])
            ]);
        }
        else {
            $result =  Yii::$app->zendesk->post('/tickets.json', [
                'body' => json_encode([
                    'ticket' => $this->getAttributes()
                ])
            ]);
            $this->id = $result['ticket']['id'];

            return $this->id;
        }
    }
}