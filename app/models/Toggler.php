<?php

/**
 * Class Toggler
 */
class Toggler extends Eloquent
{

    protected static $prefix;
    public $timestamps = false;

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('User')->select('users.id', 'users.code', 'users.full_name');
    }

    /**
     * @param $object
     * @return bool
     */
    public static function toggle($object)
    {
        $record = self::where(static::$prefix . '_type', get_class($object))
            ->where(static::$prefix . '_id', $object->id)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (isset($record)) {
            $record->delete();
            return false;

        } else {
            self::create(
                [
                    static::$prefix . '_type' => get_class($object),
                    static::$prefix . '_id'   => $object->id,
                    'user_id'                 => Auth::user()->id
                ]
            );

            return true;
        }
    }

}