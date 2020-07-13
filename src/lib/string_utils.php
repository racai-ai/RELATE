<?php

function startsWith($haystack, $needle)
{

    if(is_array($needle)){
        foreach($needle as $n){
            if(startsWith($haystack,$n))return true;
        }
        return false;
    }

     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

