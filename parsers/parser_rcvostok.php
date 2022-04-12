<?php
include 'modules/simple_html_dom.php';
include 'modules/modules.php';

// Clear Log Folder
clear('log/');

$root = "https://rcvostok.ru";

if (isset($_SERVER['argv'][2])) {
    $category = $_SERVER['argv'][2];
    get_links_category($category);
} else {
    $arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    get_links_sku($arr_sku);
}

function get_links_category($category)
{
    $category_html = file_get_html($category);
    $products = $category_html->find('div.catalog-item-card__title a');

    foreach ($products as $key => $value) {
        $url = $GLOBALS['root'] . $value->href;
        $sku = get_sku($url);
        $node = 'img[class="zoom-foto"]';
        $attr = 'src';
        $root = null;

        $response = parse_images(
            $url,
            $sku,
            $node,
            $attr,
            $root
        );

        logit($value, $url, $response);
        echo "Done $url\n";
        sleep(rand(3, 10));
    }

    exit("*** All Done! ***");
}

function get_links_sku($arr_sku)
{
    foreach ($arr_sku as $key => $value) {
        $headers = get_headers("https://rcvostok.ru/search?q=$value&catalog_id=");
        $url = substr($headers[9], 10); // Trim 'Location: '
        $sku = $value;
        $node = 'img[class="zoom-foto"]';
        $attr = 'src';
        $root = null;

        $response = parse_images(
            $url,
            $sku,
            $node,
            $attr,
            $root
        );

        logit($value, $url, $response);
        echo "Done $url\n";
        sleep(rand(3, 10));
    }
    exit("*** All Done! ***");
}

function get_sku($url)
{
    $html = file_get_html($url);
    $str = $html->find('#properties > div:nth-child(1) > p:nth-child(2) > span.product_properties_value', 0)->plaintext; // raw sku string
    preg_match('/\d+-\d+/', $str, $sku);
    return $sku[0];
}
