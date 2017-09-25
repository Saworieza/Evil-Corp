<?php

/**
 * Class Attachment
 */
class Attachment extends Eloquent
{

    /**
     * @var string
     */
    protected $table = 'attachments';

    /**
     * @var array
     */
    protected $hidden = ['id', 'created_at', 'updated_at', 'attachabel_type', 'attachable_id', 'storage', 'status'];


    public static function boot()
    {
        parent::boot();

        self::deleted(
            function ($model) {
                $fileName = $model->code . '_' . $model->file_name;
                if (file_exists(filesFolder() . $fileName)) {
                    unlink(filesFolder() . $fileName);
                }
                if (file_exists(filesFolder() . 'p_' . $fileName)) {
                    unlink(filesFolder() . 'p_' . $fileName);
                }
            }
        );

    }

    /**
     * @param $code
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getFileByCode($code)
    {
        return self::where('code', $code)->first();

    }

    /**
     * @param $objClass
     * @param $objId
     */
    public static function processInput($objClass, $objId)
    {
        if (!Input::has('files')) {
            return;
        }

        $files = Input::get('files');
        foreach ($files as $file) {

            $att = static::where('code', $file['id'])
                ->where('attachable_id', 0)
                ->first();

            if (!empty($att)) {
                $att->attachable_type = $objClass;
                $att->attachable_id   = $objId;
                $att->description     = isset($file['description']) ? $file['description'] : '';
                $att->save();
            }


        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('User')->select('users.id', 'users.code', 'users.full_name');
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array              = parent::toArray();
        $array['file_type'] = $this->filetype;
        return $array;
    }

    /**
     * @return string
     */
    public function getFileTypeAttribute()
    {
        if(extension_loaded('mbstring') && extension_loaded('exif') && extension_loaded('fileinfo')) {
            $imgExt = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
            if (in_array($this->file_ext, $imgExt)) {
                return 'image';
            }
        }
        return "file";
    }

}
