<?php
include 'modules/simple_html_dom.php';

$arr_sku = file('sku_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$root = "http://www.atcom.com.ru";

function get_links($arr_sku)
{
    foreach ($arr_sku as $key => $value) {
        $page = "http://www.atcom.com.ru/product/$value/";

        parse_page($page, $value);

        sleep(3);
    }
    exit("*** All Done! ***");
}

function create_csv_header()
{
    $csv = array(
        '"post_title"',
        '"post_excerpt"',
        '"sku"',
        '"images"',
        '"post_content"',
        '"regular_price"',
        '"comment_status"',
        '"tax:product_cat"',
        '"attribute:pa_vendor"',
        '"attribute_data:pa_vendor"',
        '"attribute:pa_tip-kabelya"',
        '"attribute_data:pa_tip-kabelya"',
    );
    $csv = implode(',', $csv) . "\n";
    file_put_contents('log/parsed_list.csv', $csv, FILE_APPEND);
}

function parse_page($page, $value)
{
    $html = file_get_html($page);

    if (!$html) {
        file_put_contents('log/failed_list.txt', "$value\n", FILE_APPEND);
        return;
    }

    $sku = $value;
    $images = parse_img($html);
    $post_title = parse_title($html);
    $post_content = parse_description($html);
    $post_excerpt = get_exerpt($post_content);
    $tax_product_cat = 'Кабели и переходники';
    $attribute_pa_vendor = parse_brand($html);
    $attribute_data_pa_vendor = '0|1|0'; // position|visible|variation
    $attribute_pa_tip_kabelya = parse_type($html);
    $attribute_data_pa_tip_kabelya = '1|1|0'; // position|visible|variation
    $comment_status = ''; // turn off comments
    $regular_price = '1';

    $csv_string = "\"$post_title\",\"$post_excerpt\",\"$sku\",\"$images\",\"$post_content\",\"$regular_price\",\"$comment_status\",\"$tax_product_cat\",\"$attribute_pa_vendor\",\"$attribute_data_pa_vendor\",\"$attribute_pa_tip_kabelya\",\"$attribute_data_pa_tip_kabelya\"" . "\n";

    file_put_contents('log/parsed_list.csv', $csv_string, FILE_APPEND);

    echo "Done $page\n";
}

function get_exerpt($str)
{
    $length = 100;
    $postfix = '...';
    $encoding = 'UTF-8';

    if (mb_strlen($str, $encoding) <= $length) {
        return $str;
    }

    $tmp = mb_substr($str, 0, $length, $encoding);
    return mb_substr($tmp, 0, mb_strripos($tmp, ' ', 0, $encoding), $encoding) . $postfix;
}

function parse_img($html)
{
    $img_arr = $html->find('a[class="big-slide"]');
    $img_str = [];
    foreach ($img_arr as $key => $value) {
        $link = $value->attr['href'];
        $ext = pathinfo($link, PATHINFO_EXTENSION);

        if (!preg_match("/png|jpg|jpeg/i", $ext)) {
            echo "Not an image\n";
            continue;
        }
        array_push($img_str, $link);
    }
    return implode(' | ', $img_str);
}

function parse_title($html)
{
    $post_title = $html->find('div[class="product-header"]>h2', 0)->innertext;
    $post_title = str_replace('<=>', '/', $post_title);
    $post_title = strip_tags($post_title);
    $post_title = trim($post_title);
    $post_title = str_replace('&nbsp;', ' ', $post_title);
    $post_title = preg_replace('/\s+/', ' ', $post_title);
    return $post_title;
}

function parse_description($html)
{
    $description = $html->find('div[id=part2]', 0)->children;
    $arr_desc = [];
    foreach ($description as $key => $value) {
        $text = $value->innertext;
        array_push($arr_desc, $text);
    }
    $post_content = implode(' ', $arr_desc);
    return $post_content;
}

function parse_brand($html)
{
    $brand = $html->find('div[id=part1] > div[class=pr-info]', 0)->children(0)->children(1)->innertext;
    return $brand;
}

function parse_type($html)
{
    $type = $html->find('div[id=part1] > div[class=pr-info]', 0)->children(1)->children(1)->innertext;
    return $type;
}

create_csv_header();
get_links($arr_sku);
