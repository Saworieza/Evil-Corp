<?php

namespace custom\helpers;

use Illuminate\Support\Facades\URL;
use Intervention\Image\Gd\Decoder;

class UrlInfo {

        protected $image = '';
        protected $description = '';
        protected $title = '';
        protected $icon = '';
        protected $url = '';
        protected $host = '';



    private function getMetaTags($url){
        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 5 )
        ));

        $html = @file_get_contents($url,0,$ctx);
      //  $html = mb_convert_encoding($html, 'UTF-8');

       // echo '<textarea rows="10" cols="300">'.$html. '</textarea>';

        if(!$html) {
            return false;
        }

        $ret = [];
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $nodes = $doc->getElementsByTagName('title');
        $title = $nodes->item(0)->nodeValue;

        $ret['title'] =  $title;

        $metas = $doc->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i++)
        {
            $content = $metas->item($i)->getAttribute('content');

            if(!empty($content)){

                $name = $metas->item($i)->getAttribute('name');
                $propery = $metas->item($i)->getAttribute('property');

                if(! empty( $name )){
                    $key = $metas->item($i)->getAttribute('name');
                } elseif(! empty( $propery )){
                    $key = $metas->item($i)->getAttribute('property');
                } else {
                    $key ='unknown';
                }
                $ret[strtolower($key)]= $content;
            }
        }

        return $ret;
    }



    public static function parse($url)
    {
        $instance = new static;

        if(substr($url, 0,4) != 'http' && substr($url, 0,5) != 'https' )
        {
            $url = 'http://'.$url;
        }

        try {
            $meta =  $instance->getMetaTags($url);

            if(!$meta) {
                return false;
            }

            $instance->imaage = '';
            $instance->description = '';
            $instance->title = '';
            $instance->icon = '';
            $instance->url = $url;

            $instance->image =setFirst([
                @$meta['twitter:image'],
                @$meta['twitter:image:src'],
                @$meta['og:image'],
            ], '');

            $instance->title =  utf8_decode(setFirst([
                @$meta['twitter:title'],
                @$meta['title']
            ], $url));

            $instance->description =  utf8_decode(setFirst([
                @$meta['twitter:description'],
                @$meta['description'],
            ],''));

            $instance->host = getUrlHost($url);
            $instance->icon = setFirstEq($instance->host, ['www.youtube.com'=>'fa fa-youtube', 'vimeo.com'=>'fa fa-vimeo', 'www.flickr.com'=>'fa fa-flickr'], '');

            return $instance;
        } catch (exception $e) {
            return false;
        }
    }

    public function toArray()
    {
        return ['url'=>$this->url, 'image'=>$this->image, 'description'=>$this->description, 'title'=>$this->title];
    }


}