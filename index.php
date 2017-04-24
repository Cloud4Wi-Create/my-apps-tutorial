<?php

define('C4W_ENV_SPLASHPORTAL_URL', getenv('C4W_ENV_SPLASHPORTAL_URL'));
define('C4W_ENV_CONTROLPANEL_URL', getenv('C4W_ENV_CONTROLPANEL_URL'));
define('C4W_ENV_MYAPPS_GET_SK_URL', getenv('C4W_ENV_MYAPPS_GET_SK_URL'));

$getSessionDataUrl = C4W_ENV_CONTROLPANEL_URL . C4W_ENV_MYAPPS_GET_SK_URL . $_GET['sk'];

$salutation = NULL;
$firstName = NULL;
$lastName = NULL;

?>

<?php

/**
 * @description: This function will not take any parameters,
 * it will just call the C4W API and return the data.  If the
 * SK is not set in the URL or if the API call is not a successful one,
 * then it will return a false boolean. Otherwise it will return the object
 *
 * @return Object | Boolean;
 */

function callApi() {
    $sk = $_GET['sk'];

    // Check to see if any of the places where SK is set exists
    if(isset($sk) && !empty($sk)) {

        // Concatenate URL
        $url = C4W_ENV_CONTROLPANEL_URL . C4W_ENV_MYAPPS_GET_SK_URL . $sk; // https://volare.cloud4wi.com/controlpanel/1.0/bridge/sessions

        // Barebones call to the API using PHP curl
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        $result = curl_exec($curl);
        return $result;
        $session = json_encode($result, true);

        return $session;

        // Create customer variable
        $c4w = array();
        if(isset($session['data']) && !empty($session['data'])) {
            $c4w = $session['data'];
        }

        // Return false if status of API call is not success
        if($session['status'] == 'success') {
            return $c4w;
        }
    }

    return false;
}

?>

<?php
/**
 * This function is where the magic will happen -
 * It will take all the data that we have an process it,
 * and based on certain conditionals it will produce a
 * string for the end-customer
 *
 * First, we will check for a cookie in the browser
 * If it's there, we will check how many times the user has been here
 *
 * Then, we will provide a coupon based on whether or not we offered
 *
 * If there is no cookie, then we just say "Hello" with a description
 * of the coffee shop, and with their name welcoming them for the first time
 *
 * @param $data
 * @return String;
 */
function setSalutation($data) {
    $user = $data['user'];

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

?>

<?php

$data = callApi();
if($data != false) {
    $salutation = setSalutation($data);
}
?>

<script>
    console.log(<?php echo json_encode($data); ?>);
</script>
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
                <p id="greeting">

                </p>
            </div>
        </div>
    </div>

</div>



<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.2.1.js" crossorigin="anonymous"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js" crossorigin="anonymous"></script>
<script src="https://splashportal.cloud4wi.com/myapps/v1/myapps-sdk.js"></script>


<script>
    var config = <?php echo json_encode($data); ?>;

    /**
     * "customer":{

            "lang":"eng",

            "is_logged":true,

            "id":"rlC.6yTePhzYg",

            "first_name":"John",

            "last_name":"Doe",

            "username":"706B5C1D",

            "gender":"",

            "birth_date":"0000-00-00 00:00:00",

            "phone":"",

            "phone_prefix":"",

            "email":"john.doe@cloud4wi.com",

            "mac_address":[]

        },

     */

    /**
     * Takes a string and inserts variables inbetween the brackets
     * @solution
     * Split the string with regex to create individual
     *
     * @param string: String
     * @param object: Object
     *
     * @return String
     */
    function insertMessageVariables(string, object) {
        var arr = string.split(/{|}/);

//        string.replace(/{|}/, function(match) {
//            return !!object[match] ? object[match] :
//        });

        for(var x = 0; x < arr.length; x++) {
            if(!!object[arr[x]]) {
                arr[x] = object[arr[x]];
            }
        }
        return arr.join('');
    }

    /**
     * To check and see if there are any messages already stored
     * in order to pre-populate inputs.
     *
     * If there are values for either the pre or post authentication
     * messages, then it will populate them individually. One
     * does not affect the other.
     */
    $.ajax({
        url:'/api.php',
        data: {
            // tenant id from config object returned from c4w api
            tenantId:config.data.tenant.tenant_id,
            action:'get_messages'
        },
        success:function(data) {
            data = typeof(data) === 'string' ? JSON.parse(data) : data;

            var greetingContainer = $("#greeting");
            var message; // just in case we have to change this in the if statement

            if(data.status === 'success') {
                if(!config.data.customer.is_logged) {
                    greetingContainer.text(data.value.pre);
                }
                if(config.data.customer.is_logged) {
                    // Process the message to find the brackets and replace them with variables
                    message = insertMessageVariables(data.value.post, config.data.customer);
                    greetingContainer.text(message);
                }
            }
        },
        method:'GET'
    });
</script>


<script type="text/javascript">
    var params = {
        fontColor:'black',
        backgroundColor:'white',
        apn:'Coffee Works',
        nextBtn:5
    };

    MYAPPS.renderNavbar(params);
    document.body.style.marginTop = "50px";
    document.body.style.fontFamily = '"Times New Roman", "Times", "Georgia", Helvetica, Arial, sans-serif';
</script>
</body>
</html>