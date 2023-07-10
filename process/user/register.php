<?php
    session_start();

    require_once(__DIR__ . "/../../config.php");
    require_once(__DIR__ . "/../../include/Message.php");
    require_once(__DIR__ . "/../../include/db.php");
    require_once(__DIR__ . "/../../include/User.php");
    require_once(__DIR__ . "/../../include/UserDAO.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $message = new Message();

    if($userDao->getLoggedUser())
    {
        header("Location: /home.php");
        exit();
    }

    $name = filter_input(INPUT_POST, "name");
    $email = filter_input(INPUT_POST, "email");
    $password = filter_input(INPUT_POST, "password");
    $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

    if(empty($name) || empty($email) || empty($password) || empty($confirmpassword))
    {
        $message->set("Invalid information", Message::TYPE_ERROR);

        header("Location: /");
        exit();
    }

    if(strlen($name) < 3 || strlen($name) > 20)
    {
        $message->set("The username needs to have between 3 and 20 characters", Message::TYPE_ERROR);

        header("Location: /");
        exit();
    }

    if($userDao->getUserByName($name))
    {
        $message->set("This username is already in use", Message::TYPE_ERROR);

        header("Location: /");
        exit();
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $message->set("Invalid email", Message::TYPE_ERROR);

        header("Location: /");
        exit();
    }

    if($userDao->getUserByEmail($email))
    {
        $message->set("This email is already in use", Message::TYPE_ERROR);

        header("Location: /");
        exit();
    }

    if(strlen($password) < 8 || strlen($password) > 20)
    {
        $message->set("The password needs to have between 8 and 20 characters", Message::TYPE_ERROR);

        header("Location: /");
        exit();
    }

    if($password !== $confirmpassword)
    {
        $message->set("The passwords you entered do not match", Message::TYPE_ERROR);

        header("Location: /");
        exit();
    }

    $user = new User();

    $user->setName($name);
    $user->setEmail($email);
    $user->setPassword($password);

    $userId = $userDao->createUser($user);

    $_SESSION["token"] = $userDao->createToken($userId, date("Y-m-d H:i:s", strtotime("1 day")));

    $message->set("Welcome!", Message::TYPE_SUCCESS);

    header("Location: /home.php");
?>
