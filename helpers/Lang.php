<?php

namespace App\Helpers;

/**
 * Class Lang
 * @package App\Helpers
 */
class Lang
{
    /**
     * @param $trans
     * @param array $placeHolders
     * @return string
     */
    public static function get($trans, $placeHolders = null)
    {
        $trans=explode('.', $trans);
        $file = array_shift($trans);
        $key = array_pop($trans);

        $translate = self::getTranslate($file)[$key] ?? 'Undefined translation.';

        if ($placeHolders) {
            $keys = array_keys($placeHolders);
            $values = array_values($placeHolders);

            return str_replace(':', '', str_replace( $keys, $values, $translate));
        }

        return $translate;
    }

    /**
     * @param $file
     * @return mixed
     */
    public static function getTranslate($file)
    {
        $locale = Application::getLocale();

        return require __DIR__ . "/../Resources/lang/{$locale}/{$file}.php";
    }
}