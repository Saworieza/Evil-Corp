<?php

use custom\helpers\Midrepo;
use custom\helpers\Responder;

/**
 * Class LikeController
 */
class LikeController extends BaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $object = Midrepo::getOrFail('object');

        $like = Likes::toggle($object);

        return Responder::json(true)->withData($like)->send();

    }

}
