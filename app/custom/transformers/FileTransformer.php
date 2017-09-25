<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class FileTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;
        $ret['icon']  =  getFileTypeIconUrl($ret['file_ext']);
        $ret['large_icon'] = null;
        $ret['image'] = null;
        if(in_array($item['file_ext'], ['png','jpg', 'jpeg', 'gif']))
        {
            $ret['image'] =  url('api/v1/file', [$item['code'],'true']);
        } else {
            $ret['large_icon'] = getFileTypeIconUrl($ret['file_ext'], true);
        }

        $ret['checked'] = "0";

        if(isset($item['user']['full_name']))
        {
            $ret['user'] = $item['user']['full_name'];
        } else {
            $ret['user'] = $item['full_name'];
            unset($ret['full_name']);
        }

        return $ret;
    }

} 