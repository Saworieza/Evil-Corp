<?php

/**
 * Class Location
 */
class Location extends Content
{

    protected $classId = 6;

    public function createFromInput()
    {
        parent::createFromInput();

        $this->setRule('location.done', 'accepted');
        $this->setRule('location.address', 'required');

        $address = Input::get('location.address');
        $zoom    = Input::get('location.zoom', 14);
        $link    = "http://maps.google.com/maps?z={$zoom}&q={$address}";

        $data = [
            'image'   => Input::get('location.image'),
            'address' => Input::get('location.address'),
            'link'    => $link
        ];

        $this->content_data = json_encode($data);

    }
}
