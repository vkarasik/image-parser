<?php
include 'modules/simple_html_dom.php';
include 'modules/modules.php';

// Clear Log Folder
clear('log/');

$root = "https://printer-plotter.ru";

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
        $search = $GLOBALS['root'] . "/poisk/?searchstring=$value";
        $html = file_get_html($search);
        $url = $GLOBALS['root'] . $html->find('a[class=cat-product-name]', 0)->href;

        $sku = $value;
        $node = '.gallery';
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
