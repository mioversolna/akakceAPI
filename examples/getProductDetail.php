<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["query"]) && !empty($_POST["query"])) {
    include($_SERVER['DOCUMENT_ROOT']."/akakceAPI.php");
    $akakceAPI = new akakceAPI;
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="ISO-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün detay</title>
</head>

<body>
    <form method="POST">
        <input placeholder="ürün detay sorgula" name="query" value="<?php echo ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["query"]) && !empty($_POST["query"]) ? $_POST["query"] : null); ?>" />
    </form>
    <pre>
        <code>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["query"]) && !empty($_POST["query"])) {
            print_r($akakceAPI->AA_GetProductDetail($_POST["query"]));
        }
        ?>
        </code>
    </pre>
</body>

</html>