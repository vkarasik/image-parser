<?php
include 'modules/simple_html_dom.php';
include 'modules/modules.php';

$arr_sku = get_array_sku();

function get_links($arr_sku)
{
    file_put_contents('parsed_list.csv', '');

    foreach ($arr_sku as $key => $value) {
        $search = file_get_contents("https://catalog.onliner.by/sdapi/catalog.api/search/products?query=$value");
        $data = json_decode($search, true);
        $link = '';

        if (isset($data["products"][0])) {
            $name = $data["products"][0]["full_name"];
            $link = $data["products"][0]["html_url"];
            $string = "$name;$value;$link\n";
        } else {
            $name = "Артикул не найден";
            $string = "$name;$value\n";
        }

        $string = "$name;$value;$link\n";
        file_put_contents('log/parsed_list.csv', $string, FILE_APPEND);
        echo $string;
        sleep(rand(3, 5));
    }
    exit("*** All Done! ***");
}

get_links($arr_sku);
