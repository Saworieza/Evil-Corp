<?php

use custom\helpers\Notificator;

/**
 * Class UserMessage
 */
class UserMessage extends BaseModel
{

    protected $table = 'messages';
    protected $fillable = ['from_id', 'to_id', 'body'];

    /**
     * @param $msgKind
     * @param $to
     * @param $body
     */
    static function send($msgKind, $to, $body)
    {

        $msg          = new self;
        $msg->to_id   = $to;
        $msg->from_id = $msgKind == MSG_PRIVATE ? Auth::user()->id : null;
        $msg->body    = $body;
        $msg->save();

        Notificator::privateMsg($to, $body);

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromUser()
    {
        return $this->belongsTo('user', 'from_id', 'id');

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toUser()
    {
        return $this->belongsTo('user', 'to_id', 'id');

    }

}