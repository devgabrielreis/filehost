<?php
    session_start();

    require_once(__DIR__ . "/config.php");
    require_once(__DIR__ . "/include/db.php");
    require_once(__DIR__ . "/include/UserDAO.php");
    require_once(__DIR__ . "/include/Message.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $message = new Message();

    $loggedUser = $userDao->getLoggedUser();

    if(!$loggedUser)
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: /");
        exit();
    }
?>

<?php require_once(__DIR__ . "/templates/header.php"); ?>

<div>
    <h3>Change username</h3>
    <form action="/process/user/change_name.php" method="POST">
        <div>
            <label for="name">New username:</label>
            <input type="text" id="name" name="name" placeholder="Enter your new username" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <input type="submit" value="Change">
    </form>
</div>

<div>
    <h3>Change email</h3>
    <form action="/process/user/change_email.php" method="POST">
        <div>
            <label for="email">New email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your new email" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <input type="submit" value="Change">
    </form>
</div>

<div>
    <h3>Change password</h3>
    <form action="/process/user/change_password.php" method="POST">
        <div>
            <label for="password">Old password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your old password" required>
        </div>

        <div>
            <label for="newpassword">New password:</label>
            <input type="password" id="newpassword" name="newpassword" placeholder="Enter your new password" required>
        </div>

        <div>
            <label for="confirmpassword">Confirm password:</label>
            <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm your new password" required>
        </div>

        <input type="submit" value="Change">
    </form>
</div>

<?php require_once(__DIR__ . "/templates/footer.php"); ?>
