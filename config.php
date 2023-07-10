<?php
    $envVarNames = json_decode(file_get_contents(__DIR__ . "/config.json"), true);

    define("DB_NAME", getenv($envVarNames["dbNameEnv"]));
    define("DB_HOST", getenv($envVarNames["dbHostEnv"]));
    define("DB_USER", getenv($envVarNames["dbUserEnv"]));
    define("DB_PASS", getenv($envVarNames["dbPassEnv"]));
    define("FILES_ROOT", getenv($envVarNames["filesRootPath"]));

    unset($envVarNames);
?>
