<?php
    session_start();

    require_once(__DIR__ . "/config.php");
    require_once(__DIR__ . "/include/UserDAO.php");
    require_once(__DIR__ . "/include/db.php");

    $userDao = new UserDAO(createDatabaseConnection());

    if($userDao->getLoggedUser())
    {
        header("Location: /home.php");
        exit();
    }
?>

<?php require_once(__DIR__ . "/templates/header.php"); ?>

<div>
    <h3>Sign in</h3>
    <form action="/process/user/login.php" method="POST">
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <p>Don't have an account yet? <span>Register</span>.</p>

        <input type="submit" value="Sign in">
    </form>
</div>

<div>
    <h3>Create account</h3>
    <form action="/process/user/register.php" method="POST">
        <div>
            <label for="name">Username:</label>
            <input type="text" id="name" name="name" placeholder="Enter your username" required>
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <div>
            <label for="confirmpassword">Confirm password:</label>
            <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm your password" required>
        </div>

        <p>Already have an account? <span>Sign in</span>.</p>

        <input type="submit" value="Register">
    </form>
</div>

<?php require_once(__DIR__ . "/templates/footer.php"); ?>
