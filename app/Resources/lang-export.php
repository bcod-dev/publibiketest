<?php
include "lang.config.php";
include "lang.php";

function arr2ini(array $a, array $parent = array())
{
    $out = '';
    foreach ($a as $k => $v)
    {
        if (is_array($v))
        {
            //subsection case
            //merge all the sections into one array...
            $sec = array_merge((array) $parent, (array) $k);
            //add section information to the output
            $out .= PHP_EOL.'[' . join('.', $sec) . ']' . PHP_EOL;
            //recursively traverse deeper
            $out .= arr2ini($v, $sec);
        }
        else
        {
            //plain key->value case
            $out .= "$k=\"$v\"" . PHP_EOL;
        }
    }
    return $out;
}


$langArray  = _LANG;
foreach($langArray as $key => $langs ){
    foreach(_SUPPORTED_LANG as $langCode => $langText){
        if(!isset($langs[$langCode])){
            $langArray[$key][$langCode] = "";
        }
    }
}
// $text = "";
// if(is_array(_LANG)){
//     foreach(_LANG as $key => $langs )
//     {
//         $text .= "key    =    ".$key."\r\n";
//         if(is_array($langs)){
//             foreach(_SUPPORTED_LANG as $langCode => $langText)
//             {
//                 $trans = isset($langs[$langCode]) ? $langs[$langCode] : "";
//                 $text .= $langCode."    =    ".$trans."\r\n";
//             }
//         }
//         $text .= _DELIMITER."\r\n";
        
//     }
// }

$text = arr2ini($langArray);


file_put_contents("lang.txt", $text);