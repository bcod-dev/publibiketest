<?php
//echo "haha\n"; //To test if this is included multiple time
use TinyFw\Lang;
function trans($key){
    return Lang::instance()->trans($key);
}

function jsLang()
{
    $data = Lang::instance()->getTranslateData();
    $result =  json_encode($data, JSON_FORCE_OBJECT);
    return $result;
}