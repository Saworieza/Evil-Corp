<?php

/**
 * Class Poll
 */
class Poll extends Content
{

    protected $classId = CONTENT_POLL;

    public function createFromInput()
    {
        parent::createFromInput();

        $this->setRule('poll', 'required');
        $data = Input::get('poll');

        $data['options']    = array_flatten(array_filter($data['options'], 'strlen'));
        $this->content_data = json_encode($data);

    }

}

