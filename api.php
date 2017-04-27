<?php

require_once('env-config.php');

function set_messages($pre, $post, $tenantId) {
    $file = constant('DATA_FILE');

    $messages = array("pre" => $pre, "post" => $post);

    if(file_exists($file)) {
        $json = json_decode(file_get_contents($file), true);
    } else {
        $json = array();
    }

    $json[$tenantId] = $messages;

    file_put_contents($file, json_encode($json));

    return $json[$tenantId];
}

function get_messages($tenantId) {
    $file = constant('DATA_FILE');

    if(file_exists($file)) {
        $json = json_decode(file_get_contents($file), true);

        if(isset($json[$tenantId]) && !empty($json[$tenantId])) {
            $json = $json[$tenantId];
            return $json;
        }
    } else {
        return array(
            'pre' => constant('DEFAULT_PRE_AUTH_MESSAGE'),
            'post' => constant('DEFAULT_POST_AUTH_MESSAGE')
        );
    }

    return "tenant not found";
}

function get_tenant($tenantId) {
    $file = constant('DATA_FILE');

    $json = json_decode(file_get_contents($file), true);

}

switch ($_GET["action"]) {
    case "get_messages":
        $value = get_messages($_GET['tenantId']);
        $status = 'success';
        break;
    case "set_messages":
        if(isset($_GET["post"]) && isset($_GET["pre"])) {
            $value = set_messages($_GET["pre"], $_GET["post"], $_GET['tenantId']);
            $status = 'success';
        } else {
            $value = "Missing argument";
            $status = 'error';
        }
        break;

    default:
        $value = "Incorrect action call";
        $status = 'error';
        break;
}

exit(json_encode(array(
    "value"=>$value,
    "status"=>$status,
    "action"=>$_GET['action']
)));
