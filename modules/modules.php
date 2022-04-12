<?php

function get_array_sku()
{
    $arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $arr_sku;
}

function clear($path)
{
    if (file_exists($path)) {
        foreach (glob("$path*") as $file) {
            unlink($file);
        }
    }
}

function logit($name, $url, $response)
{
    $string = "$name;$url;$response\n";
    file_put_contents('log/parsed_list.csv', $string, FILE_APPEND);
}

function parse_images(
    $url,
    $sku,
    $node,
    $attr,
    $root
) {
    $dirname = "images/$sku";
    mkdir($dirname);

    $html = file_get_html($url);
    if (!$html) {
        echo "Page not found\n";
        return '404';
    }

    $list = $html->find($node);

    $root = isset($root) ? $root : '';

    foreach ($list as $key => $value) {
        $link = $root . $value->attr[$attr];
        $ext = pathinfo($link, PATHINFO_EXTENSION);
        $ext = preg_replace('/(\?v=\d*)$/', '', $ext);
        if (!preg_match("/png|jpg|jpeg/i", $ext)) {
            echo "Not an image\n";
            continue;
        }

        $path = "$dirname/$sku-$key.$ext";
        copy($link, $path);
        echo "Dowloded $dirname/$sku-$key.$ext\n";
    }
    return 'ok';
}
