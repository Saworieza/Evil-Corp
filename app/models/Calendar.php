<?php

class Calendar extends Eloquent
{

    protected $table = 'calendar';

    public $timestamps = false;

    protected $fillable = ['calendarable_type', 'calendarable_id', 'start_date', 'end_date', 'all_day'];


    public function calendarable()
    {
        return $this->morphTo();
    }

    public function attendants()
    {
        return $this->hasMany('Attendant');
    }

}