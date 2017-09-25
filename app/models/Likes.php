<?php

/**
 * Class Likes
 */
class Likes extends Toggler
{

    protected static $prefix = 'likeable';
    protected $table = 'likes';
    protected $fillable = ['likeable_type', 'likeable_id', 'user_id'];
    protected $hidden = ['likeable_id', 'likeable_type', 'user_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }

}
