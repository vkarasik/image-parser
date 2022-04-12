<?php


set_time_limit(0);

$resources = array_slice(scandir('parsers'), 2);

if (isset($_POST) && isset($_POST['resource'])) {

    $sku_list = explode("\n", $_POST['sku']);
    $resource = $_POST['resource'];

    file_put_contents('sku_list.txt', '');

    foreach ($sku_list as $sku) {
        file_put_contents('sku_list.txt', "$sku\n", FILE_APPEND);
    }
    $_POST = array();
    include 'parsers' . '/' . $resource;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Parser</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            var instances = M.FormSelect.init(elems);
        });
    </script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col s6 offset-s3">
                <h1 class="center-align">Image Parser</h1>
                <form action="" method="POST">
                    <div class="input-field">
                        <textarea id="textarea1" class="materialize-textarea" name="sku"></textarea>
                        <label for="textarea1">Paste SKUs line by line</label>
                    </div>
                    <div class="input-field">
                        <select name="resource">
                            <option selected="true" disabled="disabled">Choose Resourse</option>
                            <?php foreach ($resources as $resource) : ?>
                                <option value="<?= $resource ?>"><?= $resource ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn waves-effect waves-light" type="submit" name="action">Parse</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>