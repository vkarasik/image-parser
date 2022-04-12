<?php
include 'modules/simple_html_dom.php';
include 'modules/modules.php';

// Clear Log Folder
clear('log/');

if (isset($_SERVER['argv'][2])) {
    $category = $_SERVER['argv'][2];
    $root = parse_url($category, PHP_URL_SCHEME) . '://' . parse_url($category, PHP_URL_HOST);
    get_links($category);
} else {
    $root = "https://www.somic-elec.com";
    $arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    get_links_sku($arr_sku);
}

function get_links($category)
{
    $category_html = file_get_html($category);
    $products = $category_html->find('div.cbp-vm-image > a');

    foreach ($products as $key => $value) {
        $url = $GLOBALS['root'] . $value->href;
        $sku = get_sku($url);
        $node = 'div.sp-wrap > a';
        $attr = 'href';
        $root = $GLOBALS['root'];

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

function get_links_sku($arr_sku)
{
    foreach ($arr_sku as $key => $value) {
        $search_str = "https://www.somic-elec.com/$value" . "_c0_ss";
        $search_html = file_get_html($search_str);
        $urls = $search_html->find('ul.wow > li.wow > div > div.cbp-vm-image > a');

        if (!isset($urls)) {
            echo "*** Couldn't find SKU $value ***\n";
            continue;
        }

        foreach ($urls as $link) {
            $url = $GLOBALS['root'] . '/' . $link->href;
            $sku = get_sku($url);
            $node = 'div.sp-wrap > a';
            $attr = 'href';
            // $node = '#parentHorizontalTab02 img';
            // $attr = 'src';
            $root = $GLOBALS['root'] . '/';

            $response = parse_images(
                $url,
                $sku,
                $node,
                $attr,
                $root
            );

            logit($sku, $url, $response);
        }
    }
    exit('*** All Done! ***');
}

function get_sku($url)
{
    $html = file_get_html($url);
    $str = trim($html->find('ul.ptab-list > li > span', 0)->plaintext); // raw sku string
    return $str;
}
