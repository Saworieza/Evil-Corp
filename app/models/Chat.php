<?php

/**
 * Class Chat
 */
class Chat extends BaseModel
{

    public $timestamps = true;
    protected $table = 'chat';


    public function user()
    {
        return $this->belongsTo('User');
    }
}
