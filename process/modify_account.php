<?php
    session_start();

    require_once(__DIR__ . "/../config.php");
    require_once(__DIR__ . "/../include/Message.php");
    require_once(__DIR__ . "/../include/db.php");
    require_once(__DIR__ . "/../include/User.php");
    require_once(__DIR__ . "/../include/UserDAO.php");

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

    $type = filter_input(INPUT_POST, "type");

    if($type === "changename")
    {
        $name = filter_input(INPUT_POST, "name");
        $password = filter_input(INPUT_POST, "password");

        if(!password_verify($password, $loggedUser->passwordHash))
        {
            $message->set("Invalid password", Message::TYPE_ERROR);
            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if(empty($name) || empty($password))
        {
            $message->set("Invalid information", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if(strlen($name) < 3 || strlen($name) > 20)
        {
            $message->set("The username needs to have between 3 and 20 characters", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if($userDao->getUserByName($name))
        {
            $message->set("This username is already in use", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        $userDao->changeName($loggedUser->id, $name);

        $message->set("Username changed successfully", Message::TYPE_SUCCESS);
        header("Location: " . BASE_URL . "/account.php");
    }
    elseif($type === "changeemail")
    {
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");

        if(empty($email) || empty($password))
        {
            $message->set("Invalid information", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if(!password_verify($password, $loggedUser->passwordHash))
        {
            $message->set("Invalid password", Message::TYPE_ERROR);
            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if(empty($email) || empty($password))
        {
            $message->set("Invalid information", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $message->set("Invalid email", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if($userDao->getUserByEmail($email))
        {
            $message->set("This email is already in use", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        $userDao->changeEmail($loggedUser->id, $email);

        $message->set("Email changed successfully", Message::TYPE_SUCCESS);
        header("Location: " . BASE_URL . "/account.php");
    }
    elseif($type === "changepassword")
    {
        $oldPassword = filter_input(INPUT_POST, "password");
        $newPassword = filter_input(INPUT_POST, "newpassword");
        $confirmPassword = filter_input(INPUT_POST, "confirmpassword");

        if(empty($oldPassword) || empty($newPassword) || empty($confirmPassword))
        {
            $message->set("Invalid information", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if(!password_verify($oldPassword, $loggedUser->passwordHash))
        {
            $message->set("Invalid password", Message::TYPE_ERROR);
            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if(strlen($newPassword) < 8 || strlen($newPassword) > 20)
        {
            $message->set("The password needs to have between 8 and 20 characters", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        if($newPassword !== $confirmPassword)
        {
            $message->set("The passwords you entered do not match", Message::TYPE_ERROR);

            header("Location: " . BASE_URL . "/account.php");
            exit();
        }

        $userDao->changePassword($loggedUser->id, password_hash($newPassword, PASSWORD_DEFAULT));

        $message->set("Password changed successfully", Message::TYPE_SUCCESS);
        header("Location: " . BASE_URL . "/account.php");
    }
    else
    {
        header("Location: " . BASE_URL);
        exit();
    }
?>