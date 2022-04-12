<?php
include 'modules/simple_html_dom.php';

$arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$root = "https://toymafia.ru";

function get_links($arr_sku)
{
    global $root;

    foreach ($arr_sku as $key => $value) {
        $search = file_get_html("https://toymafia.ru/catalog/?q=$value&s=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&honeypot=HoneyPotCatch");

        $page = $search->find('div.item-title > a', 0);

        if (!$page) {
            echo "*** Couldn't find SKU $value ***\n";
            file_put_contents('failed_list.csv', $value, FILE_APPEND);
            continue;
        }

        $page = $root . $page->href;

        $string = "$value;$page\n";

        file_put_contents('log/parsed_list.csv', $string, FILE_APPEND);

        parse_img($page);

        echo "Done $page\n";

        sleep(rand(3, 10));
    }
    exit("*** All Done! ***");
}

function parse_img($page)
{
    global $root;

    $html = file_get_html($page);

    $sku = $html->find('div.article > span', 1)->plaintext; // SKU

    $folder = $sku;

    mkdir("images/$folder");

    $list_aray = $html->find('a[class="popup_link fancy"]');

    foreach ($list_aray as $key => $value) {
        $link = $root . $value->attr['href'];

        $ext = pathinfo($link, PATHINFO_EXTENSION);

        if (!preg_match("/png|jpg|jpeg/i", $ext)) {

            echo "Not an image\n";

            continue;
        }

        $path = "images/$folder/$folder-$key.$ext";

        copy($link, $path);

        echo "Dowloded $folder/$folder-$key.$ext\n";

        sleep(rand(3, 10));
    }
}

get_links($arr_sku);
