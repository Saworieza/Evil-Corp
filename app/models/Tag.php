<?php

/**
 * Class Tag
 */
class Tag extends Eloquent
{

    protected $table = 'tags';
    public $timestamps = false;
    protected $fillable = ['tag', 'space_id'];
    protected $hidden = ['space_id'];

    /**
     * @param $query
     * @param $spaceId
     * @param $tag
     * @return mixed
     */
    public function scopeInSpace($query, $spaceId, $tag)
    {
        return $query->where('space_id', $spaceId)
            ->where('tag', $tag);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contents()
    {
        return $this->belongsToMany('Content', 'content_tags', 'tag_id', 'content_id');
    }


}