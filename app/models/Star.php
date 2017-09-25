<?php

/**
 * Class Star
 */
class Star extends Toggler
{

    protected $table = 'stars';
    protected static $prefix = 'starable';
    protected $fillable = ['starable_type', 'starable_id', 'user_id'];
    protected $hidden = ['starable_id', 'starable_type', 'user_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function starable()
    {
        return $this->morphTo();
    }

}