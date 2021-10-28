<?php

namespace TinyFw;
class Lang
{
    private static $instance = null;

    public $translateData = array();
    public $langs = array('en' => 'English');
    public $defaultLang  = 'en';

    /**
     * Create instance
     * @return \TinyFw\Lang
     */
    public static function & instance()
    {
        if (self::$instance == null) {
            include LANGUAGE_CONFIG;
            //include  LANGUAGE_FILE;
            $translateData = self::readLanguageFile();


            function utf8ize($d) {
                if (is_array($d)) {
                    foreach ($d as $k => $v) {
                        $d[$k] = utf8ize($v);
                    }
                } else if (is_string ($d)) {
                    return utf8_encode($d);
                }
                return $d;
            }

            $langData = utf8ize($translateData);

            $langs = defined("_SUPPORTED_LANG") ? _SUPPORTED_LANG : array();
            self::$instance = new self($langData, $langs);
        }
        return self::$instance;
    }

    public static function readLanguageFile()
    {
        if(file_exists(LANGUAGE_FILE)){
            $data = parse_ini_file(LANGUAGE_FILE, true);
            if(is_array($data)){
                return $data;
            }
        }
        return array();
    }

    public function __construct($translateData, $langs)
    {
        $this->translateData = $translateData;
        $this->langs = $langs;
    }

    public function getLang()
    {
        $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $this->defaultLang;
        $lang = in_array($lang, array_keys($this->langs)) ? $lang : $this->defaultLang;

        return $lang;
    }

    public function getTranslateData()
    {
        return $this->translateData;
    }

    public function setLang($lang)
    {
        $_SESSION['lang'] = $lang;
    }

    public function getSupportedLangs()
    {
        return $this->langs;
    }


    public function trans($key)
    {
        $lang = $this->getLang();
        $translated = $key;
        if(isset($this->translateData[$key][$lang])){
            $translated = $this->translateData[$key][$lang];
        }

        if($translated == "" || $translated == null) {
            $translated = $key;
        }
        
        return $translated;
    }
}