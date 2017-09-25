<?php

/**
 * Class Link
 */
class Link extends Content
{

    protected $classId = 1;

    public function createFromInput()
    {
        parent::createFromInput();

        $this->setRule('link.done', 'accepted');
        $this->setRule('link.url', 'required|url');

        $data = [
            'url'         => Input::get('link.url'),
            'image'       => Input::get('link.image'),
            'title'       => Input::get('link.title', ''),
            'description' => Input::get('link.description', '')
        ];

        $this->content_data = json_encode($data);

    }
}
