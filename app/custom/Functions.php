<?php

function tree($items, &$res, $parentid = null, $path=null)
{

    foreach($items as $item)
    {
        if($item['parent_id'] == $parentid)
        {
            $item['path'] = isset($path) ? $path.'/'.$item['name'] : $item['name'];
            $item['childs'] = [];
            $i = array_push($res, $item);
            tree($items, $res[$i-1]['childs'], $item['id'], $item['path']);
        }
    }

}

function getModuleFiles($fileName)
{
    $ret = [];
    $dirs = glob( app_path('modules/*') );
    if($dirs == false) return [];
    $dirs = array_filter($dirs , 'is_dir');
    foreach($dirs as $dir){
        if(file_exists($dir. '/'.$fileName))
            $ret[] = $dir. '/'.$fileName;
    }

    return $ret;
}

function getModuleTemplates()
{
    $ret = [];
    $len = strlen(app_path());
    $dirs = glob( app_path('modules/*') );
    if($dirs == false) return [];

    $dirs = array_filter($dirs, 'is_dir');
    foreach($dirs as $dir){
        $ret[]= glob($dir.'/*.html');
    }

    $ret = array_flatten($ret);
    foreach($ret as &$r)
    {
        $r = substr($r, $len+1);
    }
    return $ret;
}

function checkForCustomView($viewName)
{
    return View::exists($viewName.'-custom') ? $viewName.'-custom' : $viewName;
}

function findWhere($array, $matching) {
    foreach ($array as $item) {
        $is_match = true;
        foreach ($matching as $key => $value) {

            if (is_object($item)) {
                if (! isset($item->$key)) {
                    $is_match = false;
                    break;
                }
            } else {
                if (! isset($item[$key])) {
                    $is_match = false;
                    break;
                }
            }

            if (is_object($item)) {
                if ($item->$key != $value) {
                    $is_match = false;
                    break;
                }
            } else {
                if ($item[$key] != $value) {
                    $is_match = false;
                    break;
                }
            }
        }

        if ($is_match) {
            return $item;
        }
    }

    return false;
}

function extractTags($str, $char)
{
    preg_match_all('/(^|\s)'.$char.'[a-zA-Z0-9ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]*/i', $str, $tags);

    $res = array_map(function($item) use($char) {

        if($item == $char)
        {
            return false;

        } else {
            return  trim(trim($item),$char);

        }
    }, $tags[0] );

    $res = array_filter($res);

    return array_unique( array_values($res) );
}

function getFileTypeIcon($ext)
{
    if(file_exists(public_path().'/assets/img/fileicons/32px/'.$ext.'.png'))
    {
        return public_path().'/assets/img/fileicons/32px/'.$ext.'.png';
    } else {
        return public_path().'/assets/img/fileicons/32px/_blank.png';
    }
}

function getFileTypeIconUrl($ext, $large = false)
{
    $public = $_ENV['ROOTED_PUBLIC'] ? '/public' : '';
    $iconFolder = $public.'/assets/img/fileicons/';
    if($large) {
        $iconFolder .= '48px/';
    }  else {
        $iconFolder .= '32px/';

    }


    if(file_exists(public_path().$iconFolder.$ext.'.png'))
    {
        return asset($iconFolder.$ext.'.png');
    } else {
        return asset($iconFolder.'_blank.png');
    }
}


function getUrlHost($url){
    $url = parse_url($url);
    return $url['host'];
}

function setFirstEq($var, $list,$default){
    foreach ($list as $key => $value) {
        if( $var == $key ) return $value;
    }
    return $default;
}

function setFirst($list, $default){
    foreach ($list as $value) {
        if( isset( $value ) ) return $value;
    }

    return $default;
}

function arrayToggleElement($element, &$array)
{
    if(($k=array_search($element, $array)) !== false)
    {
        array_splice($array, $k, 1);
        $res = 0;
    } else {
        $array[] = $element;
        $res = 1;
    }

    return $res;
}
function arrayRemoveByKeyValue($key, $value, $array) {

    foreach ($array as $k => $val) {
        if ($val[$key] == $value) {
            unset($array[$k]);
        }
    }

    return $array;
}


function properCaseSentence($text)
{
    return preg_replace_callback('/([.!?])\s*(\w)/', function ($matches) {
        return strtoupper($matches[1] . ' ' . $matches[2]);
    }, ucfirst(strtolower($text)));
}

function ProcessBodyText($str, $nl2br = true)
{
    $str = strip_tags($str);
    if($nl2br)
    {
        $str = nl2br($str, false);
    }
    return $str;
}


function checkSlash($str)
{
    if( substr($str,-1) !== '/') $str .= '/';

    return $str;

}

function filesFolder()
{
    return checkSlash(Config::get('app.filesfolder'));
}

function isLocalEnv()
{
    return App::environment()=='local';
}

function now()
{
    return date("Y-m-d H:i:s");
}

function arrayFilterByPath(&$array, $filterType, $removeKeys)
{
    if(is_object($array))
    {
        $array = $array->toArray();
    }
    return internalFilterByPath($array, $filterType, $removeKeys);
}

function internalFilterByPath(&$array, $filterType, $removeKeys, $prefix="")
{
    foreach($array as $k => &$v)
    {
        if(is_array($v))
        {
//            $p = array_keys($array) == range(0, count($array) - 1) ? $prefix : $prefix.'.'.$k;
            $p = ! isAssoc($array) ? $prefix : $prefix.'.'.$k;

            internalFilterByPath($v,$filterType, $removeKeys, $p);

        } else {
            $found = in_array($prefix.'.'.$k, $removeKeys);

            if($found && $filterType == 'forget')
                unset($array[$k]);

            if((!$found) && $filterType == 'only')
                unset($array[$k]);



        }
    }
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


function isAssoc($arr)
{
    return array_keys($arr) !== range(0, count($arr) - 1);
}


function getEmailDomain($email)
{
    if( filter_var( $email, FILTER_VALIDATE_EMAIL ) )
    {
        $a = explode('@', $email);
       $domain = array_pop($a);

       return $domain;
    }

    return false;
}

function arrayGetIndexByKeyValue($key, $value, $array) {
    $i = -1;
    foreach ($array as $k => $val) {
        $i++;
        if ($val[$key] == $value) {
            return $i;
        }
    }

    return -1;

}

function getMimeType($fileExt)
{

    $fileExt = strtolower($fileExt);

    $mime_types = array("323" => "text/h323",
        "acx" => "application/internet-property-stream",
        "ai" => "application/postscript",
        "aif" => "audio/x-aiff",
        "aifc" => "audio/x-aiff",
        "aiff" => "audio/x-aiff",
        "asf" => "video/x-ms-asf",
        "asr" => "video/x-ms-asf",
        "asx" => "video/x-ms-asf",
        "au" => "audio/basic",
        "avi" => "video/x-msvideo",
        "axs" => "application/olescript",
        "bas" => "text/plain",
        "bcpio" => "application/x-bcpio",
        "bin" => "application/octet-stream",
        "bmp" => "image/bmp",
        "c" => "text/plain",
        "cat" => "application/vnd.ms-pkiseccat",
        "cdf" => "application/x-cdf",
        "cer" => "application/x-x509-ca-cert",
        "class" => "application/octet-stream",
        "clp" => "application/x-msclip",
        "cmx" => "image/x-cmx",
        "cod" => "image/cis-cod",
        "cpio" => "application/x-cpio",
        "crd" => "application/x-mscardfile",
        "crl" => "application/pkix-crl",
        "crt" => "application/x-x509-ca-cert",
        "csh" => "application/x-csh",
        "css" => "text/css",
        "dcr" => "application/x-director",
        "der" => "application/x-x509-ca-cert",
        "dir" => "application/x-director",
        "dll" => "application/x-msdownload",
        "dms" => "application/octet-stream",
        "doc" => "application/msword",
        "dot" => "application/msword",
        "dvi" => "application/x-dvi",
        "dxr" => "application/x-director",
        "eps" => "application/postscript",
        "etx" => "text/x-setext",
        "evy" => "application/envoy",
        "exe" => "application/octet-stream",
        "fif" => "application/fractals",
        "flr" => "x-world/x-vrml",
        "gif" => "image/gif",
        "gtar" => "application/x-gtar",
        "gz" => "application/x-gzip",
        "h" => "text/plain",
        "hdf" => "application/x-hdf",
        "hlp" => "application/winhlp",
        "hqx" => "application/mac-binhex40",
        "hta" => "application/hta",
        "htc" => "text/x-component",
        "htm" => "text/html",
        "html" => "text/html",
        "htt" => "text/webviewhtml",
        "ico" => "image/x-icon",
        "ief" => "image/ief",
        "iii" => "application/x-iphone",
        "ins" => "application/x-internet-signup",
        "isp" => "application/x-internet-signup",
        "jfif" => "image/pipeg",
        "jpe" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "jpg" => "image/jpeg",
        "js" => "application/x-javascript",
        "latex" => "application/x-latex",
        "lha" => "application/octet-stream",
        "lsf" => "video/x-la-asf",
        "lsx" => "video/x-la-asf",
        "lzh" => "application/octet-stream",
        "m13" => "application/x-msmediaview",
        "m14" => "application/x-msmediaview",
        "m3u" => "audio/x-mpegurl",
        "man" => "application/x-troff-man",
        "mdb" => "application/x-msaccess",
        "me" => "application/x-troff-me",
        "mht" => "message/rfc822",
        "mhtml" => "message/rfc822",
        "mid" => "audio/mid",
        "mny" => "application/x-msmoney",
        "mov" => "video/quicktime",
        "movie" => "video/x-sgi-movie",
        "mp2" => "video/mpeg",
        "mp3" => "audio/mpeg",
        "mpa" => "video/mpeg",
        "mpe" => "video/mpeg",
        "mpeg" => "video/mpeg",
        "mpg" => "video/mpeg",
        "mpp" => "application/vnd.ms-project",
        "mpv2" => "video/mpeg",
        "ms" => "application/x-troff-ms",
        "mvb" => "application/x-msmediaview",
        "nws" => "message/rfc822",
        "oda" => "application/oda",
        "p10" => "application/pkcs10",
        "p12" => "application/x-pkcs12",
        "p7b" => "application/x-pkcs7-certificates",
        "p7c" => "application/x-pkcs7-mime",
        "p7m" => "application/x-pkcs7-mime",
        "p7r" => "application/x-pkcs7-certreqresp",
        "p7s" => "application/x-pkcs7-signature",
        "pbm" => "image/x-portable-bitmap",
        "pdf" => "application/pdf",
        "pfx" => "application/x-pkcs12",
        "pgm" => "image/x-portable-graymap",
        "pko" => "application/ynd.ms-pkipko",
        "pma" => "application/x-perfmon",
        "pmc" => "application/x-perfmon",
        "pml" => "application/x-perfmon",
        "pmr" => "application/x-perfmon",
        "pmw" => "application/x-perfmon",
        "pnm" => "image/x-portable-anymap",
        "png" => "image/png",
        "pot" => "application/vnd.ms-powerpoint",
        "ppm" => "image/x-portable-pixmap",
        "pps" => "application/vnd.ms-powerpoint",
        "ppt" => "application/vnd.ms-powerpoint",
        "prf" => "application/pics-rules",
        "ps" => "application/postscript",
        "pub" => "application/x-mspublisher",
        "qt" => "video/quicktime",
        "ra" => "audio/x-pn-realaudio",
        "ram" => "audio/x-pn-realaudio",
        "ras" => "image/x-cmu-raster",
        "rgb" => "image/x-rgb",
        "rmi" => "audio/mid",
        "roff" => "application/x-troff",
        "rtf" => "application/rtf",
        "rtx" => "text/richtext",
        "scd" => "application/x-msschedule",
        "sct" => "text/scriptlet",
        "setpay" => "application/set-payment-initiation",
        "setreg" => "application/set-registration-initiation",
        "sh" => "application/x-sh",
        "shar" => "application/x-shar",
        "sit" => "application/x-stuffit",
        "snd" => "audio/basic",
        "spc" => "application/x-pkcs7-certificates",
        "spl" => "application/futuresplash",
        "src" => "application/x-wais-source",
        "sst" => "application/vnd.ms-pkicertstore",
        "stl" => "application/vnd.ms-pkistl",
        "stm" => "text/html",
        "svg" => "image/svg+xml",
        "sv4cpio" => "application/x-sv4cpio",
        "sv4crc" => "application/x-sv4crc",
        "t" => "application/x-troff",
        "tar" => "application/x-tar",
        "tcl" => "application/x-tcl",
        "tex" => "application/x-tex",
        "texi" => "application/x-texinfo",
        "texinfo" => "application/x-texinfo",
        "tgz" => "application/x-compressed",
        "tif" => "image/tiff",
        "tiff" => "image/tiff",
        "tr" => "application/x-troff",
        "trm" => "application/x-msterminal",
        "tsv" => "text/tab-separated-values",
        "txt" => "text/plain",
        "uls" => "text/iuls",
        "ustar" => "application/x-ustar",
        "vcf" => "text/x-vcard",
        "vrml" => "x-world/x-vrml",
        "wav" => "audio/x-wav",
        "wcm" => "application/vnd.ms-works",
        "wdb" => "application/vnd.ms-works",
        "wks" => "application/vnd.ms-works",
        "wmf" => "application/x-msmetafile",
        "wps" => "application/vnd.ms-works",
        "wri" => "application/x-mswrite",
        "wrl" => "x-world/x-vrml",
        "wrz" => "x-world/x-vrml",
        "xaf" => "x-world/x-vrml",
        "xbm" => "image/x-xbitmap",
        "xla" => "application/vnd.ms-excel",
        "xlc" => "application/vnd.ms-excel",
        "xlm" => "application/vnd.ms-excel",
        "xls" => "application/vnd.ms-excel",
        "xlt" => "application/vnd.ms-excel",
        "xlw" => "application/vnd.ms-excel",
        "xof" => "x-world/x-vrml",
        "xpm" => "image/x-xpixmap",
        "xwd" => "image/x-xwindowdump",
        "z" => "application/x-compress",
        "zip" => "application/zip");

    if(isset($mime_types[$fileExt])){
        return $mime_types[$fileExt];
    } else {
        return "application/octet-stream";
    }
}

function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
    if ($considerHtml) {
        // if the plain text is shorter than the maximum length, return the whole text
        if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        // splits all html-tags to scanable lines
        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
        $total_length = strlen($ending);
        $open_tags = array();
        $truncate = '';
        foreach ($lines as $line_matchings) {
            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
            if (!empty($line_matchings[1])) {
                // if it's an "empty element" with or without xhtml-conform closing slash
                if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                    // do nothing
                    // if tag is a closing tag
                } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                    // delete tag from $open_tags list
                    $pos = array_search($tag_matchings[1], $open_tags);
                    if ($pos !== false) {
                        unset($open_tags[$pos]);
                    }
                    // if tag is an opening tag
                } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                    // add tag to the beginning of $open_tags list
                    array_unshift($open_tags, strtolower($tag_matchings[1]));
                }
                // add html-tag to $truncate'd text
                $truncate .= $line_matchings[1];
            }
            // calculate the length of the plain text part of the line; handle entities as one character
            $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
            if ($total_length+$content_length> $length) {
                // the number of characters which are left
                $left = $length - $total_length;
                $entities_length = 0;
                // search for html entities
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                    // calculate the real length of all entities in the legal range
                    foreach ($entities[0] as $entity) {
                        if ($entity[1]+1-$entities_length <= $left) {
                            $left--;
                            $entities_length += strlen($entity[0]);
                        } else {
                            // no more characters left
                            break;
                        }
                    }
                }
                $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                // maximum lenght is reached, so get off the loop
                break;
            } else {
                $truncate .= $line_matchings[2];
                $total_length += $content_length;
            }
            // if the maximum length is reached, get off the loop
            if($total_length>= $length) {
                break;
            }
        }
    } else {
        if (strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = substr($text, 0, $length - strlen($ending));
        }
    }
    // if the words shouldn't be cut in the middle...
    if (!$exact) {
        // ...search the last occurance of a space...
        $spacepos = strrpos($truncate, ' ');
        if (isset($spacepos)) {
            // ...and cut the text in this position
            $truncate = substr($truncate, 0, $spacepos);
        }
    }
    // add the defined ending to the text
    $truncate .= $ending;
    if($considerHtml) {
        // close all unclosed html-tags
        foreach ($open_tags as $tag) {
            $truncate .= '</' . $tag . '>';
        }
    }
    return $truncate;
}

function my_str_split($string)
{
    $slen=strlen($string);
    for($i=0; $i<$slen; $i++)
    {
        $sArray[$i]=$string{$i};
    }
    return $sArray;
}

function noDiacritics($string)
{
    //cyrylic transcription
    $cyrylicFrom = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $cyrylicTo   = array('A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia');


    $from = array("Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž");
    $to   = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");


    $from = array_merge($from, $cyrylicFrom);
    $to   = array_merge($to, $cyrylicTo);

    $newstring=str_replace($from, $to, $string);
    return $newstring;
}

function makeSlugs($string, $maxlen=0)
{
    $newStringTab=array();
    $string=strtolower(noDiacritics($string));
    if(function_exists('str_split'))
    {
        $stringTab=str_split($string);
    }
    else
    {
        $stringTab=my_str_split($string);
    }

    $numbers=array("0","1","2","3","4","5","6","7","8","9","-");
    //$numbers=array("0","1","2","3","4","5","6","7","8","9");

    foreach($stringTab as $letter)
    {
        if(in_array($letter, range("a", "z")) || in_array($letter, $numbers))
        {
            $newStringTab[]=$letter;
            //print($letter);
        }
        elseif($letter==" ")
        {
            $newStringTab[]="-";
        }
    }

    if(count($newStringTab))
    {
        $newString=implode($newStringTab);
        if($maxlen>0)
        {
            $newString=substr($newString, 0, $maxlen);
        }

        $newString = removeDuplicates('--', '-', $newString);
    }
    else
    {
        $newString='';
    }

    return $newString;
}


function checkSlug($sSlug)
{
    if(ereg ("^[a-zA-Z0-9]+[a-zA-Z0-9\_\-]*$", $sSlug))
    {
        return true;
    }

    return false;
}

function removeDuplicates($sSearch, $sReplace, $sSubject)
{
    $i=0;
    do{

        $sSubject=str_replace($sSearch, $sReplace, $sSubject);
        $pos=strpos($sSubject, $sSearch);

        $i++;
        if($i>100)
        {
            die('removeDuplicates() loop error');
        }

    }while($pos!==false);

    return $sSubject;
}

function exportCSV($headings=false, $rows=false, $filename=false)
{
    # Ensure that we have data to be able to export the CSV
    if ((!empty($headings)) AND (!empty($rows)))
    {
        # modify the name somewhat
        $name = ($filename !== false) ? $filename . ".csv" : "export.csv";

        # Set the headers we need for this to work
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $name);

        # Start the ouput
        $output = fopen('php://output', 'w');

        # Create the headers
        fputcsv($output, $headings);

        # Then loop through the rows
        foreach($rows as $row)
        {
            # Add the rows to the body
            fputcsv($output, $row, ',', '"');
        }

        # Exit to close the stream off
        exit();
    }

    # Default to a failure
    return false;
}