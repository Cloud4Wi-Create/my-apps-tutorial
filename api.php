<?php
function set_messages($pre, $post, $tenantId) {
    $file = "messages.txt";

    $messages = array("pre" => $pre, "post" => $post);


    if(isset($json[$tenantId]) && !empty($json[$tenantId])) {
        $json[] = array(
          $tenantId => $messages
        );
    } else {
        $json[$tenantId] = $messages;
    }

    file_put_contents($file, json_encode($json));

    return $json[$tenantId];
}

function get_messages($tenantId) {
    $file = "messages.txt";

    $json = json_decode(file_get_contents($file), true);

    return $json;

    if(isset($json[$tenantId]) && !empty($json[$tenantId])) {
        $json = $json[$tenantId];
        return $json;
    }

    return "tenant not found";
}

function get_tenant($tenantId) {
    $file = "messages.txt";

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
