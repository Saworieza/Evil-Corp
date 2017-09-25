<?php
use custom\interfaces\Attachable;
use custom\observers\AttachObserver;

/**
 * Class Folder
 */
class Translation extends BaseModel
{
    protected $table = 'translatable';

    public $timestamps = false;


}