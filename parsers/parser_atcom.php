<?php
include 'modules/simple_html_dom.php';
include 'modules/modules.php';

// Clear Log Folder
clear('log/');

$root = "http://www.atcom.com.ru";

$arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
get_links($arr_sku);

function get_links($arr_sku)
{
    foreach ($arr_sku as $key => $value) {
        $url = "http://www.atcom.com.ru/product/$value/";
        $sku = $value;
        $node = 'a[class="big-slide"]';
        $attr = 'href';
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
