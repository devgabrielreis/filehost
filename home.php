<?php
    session_start();

    require_once(__DIR__ . "/config.php");
    require_once(__DIR__ . "/include/db.php");
    require_once(__DIR__ . "/include/UserDAO.php");
    require_once(__DIR__ . "/include/Message.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $message = new Message();

    if(empty($_SESSION["token"]))
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: " . BASE_URL);
        exit();
    }

    $loggedUser = $userDao->getUserByToken($_SESSION["token"]);

    if(!$loggedUser)
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: " . BASE_URL);
        exit();
    }

    var_dump($loggedUser);
?>

<?php require_once(__DIR__ . "/templates/header.php"); ?>

<p><a href="<?php echo BASE_URL; ?>/account.php">configuracoes de conta</a></p>
<p><a href="<?php echo BASE_URL; ?>/upload.php">enviar arquivo</a></p>
<p><a href="<?php echo BASE_URL; ?>/process/logout.php">logout</a></p>

<?php require_once(__DIR__ . "/templates/footer.php"); ?>
