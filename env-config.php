<?php
/**
 * These defined constants pull from a config file on the server.
 * If you don't have a config file, feel free to replace the "getenv"
 * method with the commented URLs next to them
 */

$staging = 'development';

if($staging == 'development') {
    define('C4W_ENV_SPLASHPORTAL_URL', getenv('C4W_ENV_SPLASHPORTAL_URL')); // "https://splashportal.cloud4wi.com"
    define('C4W_ENV_CONTROLPANEL_URL', getenv('C4W_ENV_CONTROLPANEL_URL')); // "https://volare.cloud4wi.com"
    define('C4W_ENV_MYAPPS_GET_SK_URL', getenv('C4W_ENV_MYAPPS_GET_SK_URL')); // "/controlpanel/1.0/bridge/sessions/"
    define('DATA_FILE', getenv('DATA_ENV_PATH'));
} else if($staging == 'production') {
    define('C4W_ENV_SPLASHPORTAL_URL', "https://splashportal.cloud4wi.com"); // "https://splashportal.cloud4wi.com"
    define('C4W_ENV_CONTROLPANEL_URL', "https://volare.cloud4wi.com"); // "https://volare.cloud4wi.com"
    define('C4W_ENV_MYAPPS_GET_SK_URL', "/controlpanel/1.0/bridge/sessions/"); // "/controlpanel/1.0/bridge/sessions/"
    define('DATA_FILE', 'data/messages.txt');
}

define('DEFAULT_PRE_AUTH_MESSAGE', 'Hello, welcome to our shop!');
define('DEFAULT_POST_AUTH_MESSAGE', 'Hello, thank you for logging in! Enjoy!');
