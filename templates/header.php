<?php
    require_once(__DIR__ . "/../config.php");
    require_once(__DIR__ . "/../include/Message.php");
    $message = new Message();
    $message->load();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileHost</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <header>
        <p>Header</p>
    </header>
    <main>
        <div>
            <?php echo $message->text . " " . $message->type . "<br>"; $message->destroy(); ?>
        </div>