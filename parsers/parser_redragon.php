<?php
include 'modules/simple_html_dom.php';
include 'modules/modules.php';

// Clear Log Folder
clear('log/');

$root = "https://redragon.ru";

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
    $products = $category_html->find('div.page-list-item > a');

    foreach ($products as $key => $value) {
        $url = $GLOBALS['root'] . $value->href;
        $sku = get_sku($url);
        $node = 'div[class="fotorama"] a';
        $attr = 'href';
        $root = $GLOBALS['root'];

        $response = parse_images(
            $url,
            $sku,
            $node,
            $attr,
            $root
        );

        logit('', $url, $response);
        echo "Done $url\n";
    }
    exit('*** All Done! ***');
}

function get_links_sku($arr_sku)
{
    foreach ($arr_sku as $key => $value) {
        $search = file_get_contents("https://redragon.ru/ajax_search?term=$value");

        if ($search === "[]") {
            echo "*** Couldn't find SKU $value ***\n";
            file_put_contents('log/failed_list.csv', "$value\n", FILE_APPEND);
            continue;
        }

        $url = json_decode($search, true);
        $url = $GLOBALS['root'] . $url[0]['url'];
        $sku = $value;
        $node = 'div[class="fotorama"] a';
        $attr = 'href';
        $root = $GLOBALS['root'];

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
    $str = $html->find('span[class="product-code"]', 0)->plaintext; // raw sku string
    preg_match('/\d+/', $str, $sku);
    return $sku[0];
}
