<?php
include 'modules/simple_html_dom.php';
include 'modules/modules.php';

// Clear Log Folder
clear('log/');

$root = "https://www.asustor.com/";

if (isset($_SERVER['argv'][2])) {
    $category = $_SERVER['argv'][2];
    get_links_category($category);
} else {
    $arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    get_links_sku($arr_sku);
}

function get_links_sku($arr_sku)
{
    foreach ($arr_sku as $key => $value) {
        $url = $value;
        $sku = get_sku($url);
        $node = 'a[class="image_b"]';
        $attr = 'rel';
        $root = '';

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
    $str = $html->find('a[id="open-p-top-menu"]', 0)->plaintext; // raw sku string
    $sku = trim($str);
    return $sku;
}
