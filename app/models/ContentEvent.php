<?php

/**
 * Class ContentEvent
 */
class ContentEvent extends Content
{

    protected $classId = 5;

    public static function boot()
    {
        parent::boot();

        self::created(
            function ($model) {
                // Do not use polymorphic relation due to inheritance
                Calendar::create(
                    [
                        "start_date"        => Input::get('event.start_date'),
                        "end_date"          => Input::get('event.end_date'),
                        "all_day"           => Input::get('event.all_day'),
                        "calendarable_id"   => $model->id,
                        "calendarable_type" => 'Content'
                    ]
                );
            }
        );
    }

    /**
     *
     */
    public function createFromInput()
    {
        parent::createFromInput();

        $this->setRule('event.start_date', 'required|date');
        $this->setRule('event.end_date', 'required|date');

        $this->content_data = json_encode(Input::get('event'));

    }
}

