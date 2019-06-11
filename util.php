<?php

function random_from($array = array(0, 1))
{
    return $array[rand(0, count($array) - 1)];
}

// Credit: https://stackoverflow.com/a/4356295
function random_string($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $range = strlen($characters) - 1;
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $range)];
    }
    return $randomString;
}

function json_from($filename = "")
{
    return json_decode(file_get_contents($filename), true);
}

function json_to_file($data = array(), $filename = "")
{
    $filehook = fopen($filename, "w");
    fwrite($filehook, json_encode($data));
    fclose($filehook);
}