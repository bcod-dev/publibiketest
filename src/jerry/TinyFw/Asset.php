<?php

namespace TinyFw;
class Asset
{
    public static function css($filepath, $dir = null)
    {
        $dir = $dir == null ? WEB_CSS_PATH : $dir;
        return self::assetPath($filepath, $dir);
    }

    public static function js($filepath, $dir = null)
    {
        $dir = $dir == null ? WEB_JS_PATH : $dir;
        return self::assetPath($filepath, $dir);
    }

    public static function img($filepath, $dir = null)
    {
        $dir = $dir == null ? WEB_IMG_PATH : $dir;
        return self::assetPath($filepath, $dir);
    }

    public static function assetPath($filepath, $dir)
    {
        $path = trim($dir, ' /') . '/' . $filepath;
        return $path;
    }

}