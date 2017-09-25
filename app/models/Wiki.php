<?php
use custom\interfaces\Attachable;
use custom\observers\AttachObserver;

/**
 * Class Wiki
 */
class Wiki extends BaseModel implements Attachable
{

    protected $table = 'wikis';
    protected $hidden = ['space_id'];

    function __construct()
    {
        parent::__construct();

        $this->setRule('title', 'required');
        $this->setRule('access', 'required|in:PR,PU');
    }

    public static function boot()
    {
        parent::boot();

        self::observe(new AttachObserver);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('user', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo('user', 'updated_by');
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