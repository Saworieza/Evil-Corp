<?php

/**
 * Class Chart
 */
class Chart extends Content
{

    protected $classId = 3;

    public function createFromInput()
    {
        parent::createFromInput();

        $this->setRule('chart', 'required');

        $this->content_data = json_encode(Input::get('chart'));

    }
}
