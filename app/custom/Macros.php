<?php

HTML::macro('scripts', function($value)
{
    $dir = public_path($value);
    $files = [];
    //attributes['src'] = $this->url->asset($url, $secure);
    if(! file_exists($dir)) return "";

    $files = glob($dir.'/*.js');

    foreach($files as &$file)
    {
        $file = HTML::script(URL::asset($value.'/'.basename($file)));
    }

    return "<!-- Warning: including $value/*.js -->".PHP_EOL.implode('', $files);

});