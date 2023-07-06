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
?>

<?php require_once(__DIR__ . "/templates/header.php"); ?>

<div>
    <form action="<?php echo BASE_URL; ?>/process/upload.php" method="POST" enctype="multipart/form-data">
        <div>
            <label for="file">File:</label>
            <input type="file" id="file" name="file" required>
        </div>

        <div>
            <label for="visibility">Visibility:</label>
            <select name="visibility" id="visibility" required>
                <option value="">Select</option>
                <option value="private">Private</option>
                <option value="restricted">Restricted</option>
                <option value="public">Public</option>
            </select>
        </div>

        <input type="submit" value="Upload">
    </form>
</div>

<?php require_once(__DIR__ . "/templates/footer.php"); ?>
