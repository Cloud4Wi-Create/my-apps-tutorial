<?php
function set_messages($pre, $post) {
    $file = "messages.txt";

    $json = array("pre" => $pre, "post" => $post);

    file_put_contents($file, json_encode($json));

    return $json;
}

function get_messages() {
    $file = "messages.txt";

    $json = json_decode(file_get_contents($file), true);

    return $json;
}

switch ($_GET["action"]) {
    case "get_messages":
        $value = get_messages();
        $status = 'success';
        break;
    case "set_messages":
        if(isset($_GET["post"]) && isset($_GET["pre"])) {
            $value = set_messages($_GET["pre"], $_GET["post"]);
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
