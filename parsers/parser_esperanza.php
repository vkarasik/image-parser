<?php

/*
To solve proplem with protocol
put code below before main code in function file_get_html() in simple_html_dom.php

$context = stream_context_create(
    array(
        'http' => array(
            'follow_location' => false
        ),
        'ssl' => array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    )
);
*/

include 'modules/simple_html_dom.php';
include 'modules/modules.php';

// Clear Log Folder
clear('log/');

if (isset($_SERVER['argv'][2])) {
    $category = $_SERVER['argv'][2];
    $root = parse_url($category, PHP_URL_SCHEME) . '://' . parse_url($category, PHP_URL_HOST);
    get_links($category);
} else {
    $root = "https://esperanza.pl";
    $arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    get_links_sku($arr_sku);
}

function get_links($category)
{
    $category_html = file_get_html($category);
    $products = $category_html->find('div.nazwa > a');

    foreach ($products as $key => $value) {
        $url = $GLOBALS['root'] . '/' . $value->href;
        preg_match('/\w+\d+\w+$/i', $value->next_sibling()->plaintext, $sku);
        $sku = trim($sku[0]);
        $node = 'a[rel="lightbox"]';
        $attr = 'href';
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
    exit('*** All Done! ***');
}

function get_links_sku($arr_sku)
{
    foreach ($arr_sku as $key => $value) {
        $search_str = "https://esperanza.pl/listaProduktow.php?dbFin=$value&szukaj=Szukaj&kat=0&idz=";
        $search_html = file_get_html($search_str);
        $url = $search_html->find('div.nazwa > a', 0);

        if (!isset($url)) {
            echo "*** Couldn't find SKU $value ***\n";
            continue;
        }

        $url = $GLOBALS['root'] . '/' . $url->href;
        $sku = $value;
        $node = 'a[rel="lightbox"]';
        $attr = 'href';
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
    exit('*** All Done! ***');
}
