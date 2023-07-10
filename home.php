<?php
    session_start();

    require_once(__DIR__ . "/config.php");
    require_once(__DIR__ . "/include/db.php");
    require_once(__DIR__ . "/include/UserDAO.php");
    require_once(__DIR__ . "/include/FileDAO.php");
    require_once(__DIR__ . "/include/Message.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $fileDao = new FileDAO(createDatabaseConnection(), $userDao);
    $message = new Message();

    $loggedUser = $userDao->getLoggedUser();

    if(!$loggedUser)
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: /");
        exit();
    }

    var_dump($loggedUser);
?>

<?php require_once(__DIR__ . "/templates/header.php"); ?>

<p><a href="/account.php">configuracoes de conta</a></p>
<p><a href="/upload.php">enviar arquivo</a></p>
<p><a href="/process/logout.php">logout</a></p>

<h3>my files</h3>
<?php foreach($fileDao->getUserFiles($loggedUser->getId()) as $file) : ?>
    <p><a href="<?php echo "/file.php?id=" . $file->getId(); ?>"><?php var_dump($file); ?></a></p>
<?php endforeach; ?>

<?php require_once(__DIR__ . "/templates/footer.php"); ?>
