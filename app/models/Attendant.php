<?php

/**
 * Class Attendant
 */
class Attendant extends Eloquent
{

    public $timestamps = false;
    protected $table = 'attendants';
    protected $fillable = ['user_id', 'calendar_id', 'attending'];

    protected $hidden = [''];

    /**
     * @param $calendarId
     * @param $assistValue
     */
    public static function upsert($calendarId, $assistValue)
    {
        $ast = static::where('user_id', Auth::user()->id)
            ->where('calendar_id', $calendarId)
            ->first();
        if (isset($ast)) {
            $ast->attending = $assistValue;
        } else {
            $ast              = new static;
            $ast->user_id     = Auth::user()->id;
            $ast->calendar_id = $calendarId;
            $ast->attending   = $assistValue;
        }

        $ast->save();

    }


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
    public function calendar()
    {
        return $this->belongsTo('Calendar');
    }

}