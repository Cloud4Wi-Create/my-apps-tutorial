<?php

define('C4W_ENV_SPLASHPORTAL_URL', getenv('C4W_ENV_SPLASHPORTAL_URL'));
define('C4W_ENV_CONTROLPANEL_URL', getenv('C4W_ENV_CONTROLPANEL_URL'));
define('C4W_ENV_MYAPPS_GET_SK_URL', getenv('C4W_ENV_MYAPPS_GET_SK_URL'));

$getSessionDataUrl = C4W_ENV_CONTROLPANEL_URL . C4W_ENV_MYAPPS_GET_SK_URL . $_GET['sk'];

$salutation = NULL;
$firstName = NULL;
$lastName = NULL;

/* Do everything if the user has a session key */
if (isset($_GET['sk']) && !empty($_GET['sk'])) {

    /* Checking the session key and then getting the session data */
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $getSessionDataUrl
    ));
    $result = curl_exec($curl);
    $session = json_decode($result, true);

    $c4w = array();
    if (isset($session['data']) && !empty($session['data'])) {
        $c4w = $session['data'];
    }

    $user = array();
    if (isset($c4w['customer']) && !empty($c4w['customer'])) {
        $user = $c4w['customer'];
    }

    if (isset($user['first_name']) && !empty($user['first_name'])) {
        $firstName = $user['first_name'];
    }

    if (isset($user['last_name']) && !empty($user['last_name'])) {
        $lastName = $user['last_name'];
    }

    if (isset($user['gender']) && !empty($user['gender'])) {
        if (in_array($user['gender'], array('male', 'm'))) {
            $salutation = "Mr.";
        } elseif (in_array($user['gender'], array('female', 'f'))) {
            $salutation = "Ms.";
        }
    }

}

$content = '<span>Hello World</span>!';

$gotUserData = $salutation || $firstName || $lastName;

if ($gotUserData) {
    $content = "<span>Hello world</span>, $salutation $firstName $lastName!";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coffee Works</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css"  crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.css"  crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css"/>
</head>
<body>
<div class="brand">Coffee Works</div>
<div class="address-bar">3481 Melrose Place | Beverly Hills, CA 90210 | 123.456.7890</div>
<div class="container">
    <div class="row">
        <div class="box">
            <div class="col-lg-12">
                <hr>
                <h2 class="intro-text text-center"><strong>Free Coffee</strong></h2>
                <hr>
                <img class="img-responsive img-border img-left" src="img/DeathtoStock_Wired1.jpg" alt="" height="100" width="100">
                <hr class="visible-xs">
                <p>
                    Present this to the register in order to get your free coffee for the loyalty you've provided us!
                </p>
            </div>
        </div>
    </div>

</div>
<!-- /.container -->

<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <p>Copyright &copy; Coffee Works 2014</p>
            </div>
        </div>
    </div>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.2.1.js"
        crossorigin="anonymous"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js" crossorigin="anonymous"></script>
<script src="https://splashportal.cloud4wi.com/myapps/v1/myapps-sdk.js"></script>


<script type="text/javascript">
    var params = {
        fontColor:'black',
        backgroundColor:'white',
        apn:'Coffee Works',
        nextBtn:5
    };

    MYAPPS.renderNavbar(params);
    document.body.style.marginTop = "50px";
</script>
</body>
</html>