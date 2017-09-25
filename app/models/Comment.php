<?php

use custom\interfaces\Likeable;
use custom\interfaces\Authorizable;
use custom\interfaces\Attachable;

/**
 * Class Comment
 */
class Comment extends BaseModel implements Likeable, Authorizable, Attachable
{

    protected $table = 'comments';
    protected $fillable = ['body', 'user_id', 'commentable_id', 'commentable_type'];
    protected $hidden = array('updated_at', 'commentable_id', 'commentable_type', 'user_id');


    public static function boot()
    {
        parent::boot();

        self::observe(new custom\observers\LikesObserver);
        self::observe(new custom\observers\AttachObserver);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * @param $interface
     * @return bool
     */
    public function authorize($interface)
    {
        if ($interface == 'Likeable') {
            if (get_class($this->commentable) == 'Content') {
                return Auth::User()->inSpace($this->commentable->space_id) >= ROLE_MEMBER;
            }

            if (get_class($this->commentable) == 'Task') {
                // TODO authorize like on task
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes()
    {
        return $this->morphMany('Likes', 'likeable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments()
    {
        return $this->morphMany('Attachment', 'attachable');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('User')->select('users.id', 'users.code', 'users.full_name');
    }
}
