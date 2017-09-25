<?php
use custom\interfaces\Attachable;
use custom\observers\AttachObserver;

/**
 * Class Folder
 */
class Folder extends BaseModel implements Attachable
{

    protected $table = 'folders';

    public $timestamps = false;

    protected $hidden = ['space_id'];

    public static function boot()
    {
        parent::boot();

        self::observe(new AttachObserver);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments()
    {
        return $this->morphMany('Attachment', 'attachable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function space()
    {
        return $this->belongsTo('Space');
    }

}