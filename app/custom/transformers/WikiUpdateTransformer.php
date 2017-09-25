<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class WikiUpdateTransformer extends Transformer {

    protected $wkt;

    function __construct()
    {
        $this->wkt = new WikiTransformer;
    }


    public function transform($item)
    {
        $ret = $this->wkt->transform($item);

        $ret['body'] = $item['body'];

        return $ret;
    }

} 