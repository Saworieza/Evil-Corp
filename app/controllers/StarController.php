<?php

use custom\helpers\Midrepo;
use custom\helpers\Responder;

/**
 * Class StarController
 */
class StarController extends BaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $object = Midrepo::getOrFail('object');

        $star = Star::toggle($object);

        return Responder::json(true)->withData($star)->send();
    }

}