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

    $fileId = filter_input(INPUT_GET, "id");

    if($fileId === null)
    {
        header("Location: /");
        exit();
    }

    $file = $fileDao->getFile($fileId);

    if($file === null)
    {
        $message->set("File not found", Message::TYPE_ERROR);

        if($loggedUser === null)
        {
            header("Location: /");
        }
        else
        {
            header("Location: /home.php");
        }

        exit();
    }

    if(!$file->hasAccess($loggedUser))
    {
        $message->set("You don't have permission to access this file", Message::TYPE_ERROR);

        if($loggedUser === null)
        {
            header("Location: /");
        }
        else
        {
            header("Location: /home.php");
        }

        exit();
    }
?>

<?php require_once(__DIR__ . "/templates/header.php"); ?>

<div>
    <p><?php echo $file->getId(); ?></p>
    <p><?php echo $file->getName(); ?></p>
    <p><?php echo $file->getSize(); ?></p>
    <p><?php echo $file->getUploadTime(); ?></p>
    <p><?php echo $file->getVisibility(); ?></p>
    <p><?php echo $file->getPath(); ?></p>
    <p><?php echo $file->getOwnerId(); ?></p>
    <p><?php var_dump($file->getAllowedUsersIds()); ?></p>
</div>

<div>
    <?php if($file->isOwner($loggedUser)) : ?>
        <p>You are the owner of this file</p>
    <?php endif; ?>
</div>

<a href="<?php echo "/process/file/download.php?id=" . $file->getId(); ?>">Download</a>

<?php require_once(__DIR__ . "/templates/footer.php"); ?>
