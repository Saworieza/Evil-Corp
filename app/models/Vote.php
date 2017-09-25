<?php

/**
 * Class Vote
 */
class Vote extends Eloquent
{

    protected $table = 'votes';
    public $timestamps = false;
    protected $fillable = ['user_id', 'content_id', 'choice'];
    protected $hidden = ['id', 'content_id', 'user_id'];

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('user')->select(['id', 'full_name']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content()
    {
        return $this->belongsTo('Content');
    }


}