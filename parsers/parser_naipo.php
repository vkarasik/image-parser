<?php
include 'modules/simple_html_dom.php';
ini_set('user_agent', 'My-Application/2.5');
include 'modules/modules.php';

// Clear Log Folder
clear('log/');

if (isset($_SERVER['argv'][2])) {
    $category = $_SERVER['argv'][2];
    $root = parse_url($category, PHP_URL_SCHEME) . '://' . parse_url($category, PHP_URL_HOST);
    get_links($category);
} else {
    $root = "https://www.naipocare.com";
    $arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    get_links_sku($arr_sku);
}

function get_links($category)
{
    $category_html = file_get_html($category);
    $products = $category_html->find('.ProductItem__Info > h2 > a');

    foreach ($products as $key => $value) {
        $url = $GLOBALS['root'] . $value->href;
        $sku = get_sku($value);
        $node = '.Product__SlideItem > div > img';
        $attr = 'data-original-src';
        $root = 'https:';


        $response = parse_images(
            $url,
            $sku,
            $node,
            $attr,
            $root
        );

        logit($sku, $url, $response);
    }
    exit('*** All Done! ***');
}

function get_links_sku($urls)
{
    foreach ($urls as $value) {
        $url = $value;
        $sku = preg_replace('/\/|,|:/', '_', $value);
        $node = '.Product__SlideItem > div > img';
        $attr = 'data-original-src';
        $root = 'https:';

        $response = parse_images(
            $url,
            $sku,
            $node,
            $attr,
            $root
        );

        logit($sku, $url, $response);
    }
    exit('*** All Done! ***');
}

function get_sku($value)
{
    $str = $value->plaintext;
    $str = preg_replace('/\/|,|git"/', ' ', $str);
    $str = preg_replace('/\s\s+/', ' ', $str);
    $str = implode(' ', array_slice(explode(' ', $str), 0, 12));
    return $str;
}
