<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('slugify'))
{
  function slugify($text) {
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text))
            return 'n-a';
        return $text;
    }
}




if ( ! function_exists('get_phrase'))
{
	function get_phrase($phrase = '') {
        $phrase = trim(strtolower($phrase));
        return ucwords(str_replace('_',' ',$phrase));
	}
}


    // -----------------------------------------------------------------------------
    // Make Slug Function    
    if (!function_exists('make_slug'))
    {
        function make_slug($string)
        {
            $lower_case_string = strtolower($string);
            $string1 = preg_replace('/[^a-zA-Z0-9 ]/s', '', $lower_case_string);
            return strtolower(preg_replace('/\s+/', ' ', $string1));        
        }
    }
