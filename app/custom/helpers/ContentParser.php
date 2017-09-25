<?php

namespace custom\helpers;

class ContentParser {

    protected $tags;

    protected $htmlText;

    function __construct($str)
    {
        preg_match_all('/(^|\s)#[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]*/i', $str, $tags);

        for($i=0; $i<count($tags[0]); ++$i)
        {
            $tags[0][$i] = ltrim($tags[0][$i]);
            $tags[0][$i] = ltrim($tags[0][$i],'#');
        }

        $this->tags = array_unique( array_values($tags[0]) );

    }


    public function getTags()
    {
        return $this->tags;
    }

    public function getHtmlText()
    {

    }

}
