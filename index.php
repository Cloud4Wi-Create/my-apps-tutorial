<?php

/**
 * These defined constants pull from a config file on the server.
 * If you don't have a config file, feel free to replace the "getenv"
 * method with the commented URLs next to them
 */
define('C4W_ENV_SPLASHPORTAL_URL', getenv('C4W_ENV_SPLASHPORTAL_URL')); // "https://splashportal.cloud4wi.com"
define('C4W_ENV_CONTROLPANEL_URL', getenv('C4W_ENV_CONTROLPANEL_URL')); // "https://volare.cloud4wi.com"
define('C4W_ENV_MYAPPS_GET_SK_URL', getenv('C4W_ENV_MYAPPS_GET_SK_URL')); // "/controlpanel/1.0/bridge/sessions/"

$getSessionDataUrl = C4W_ENV_CONTROLPANEL_URL . C4W_ENV_MYAPPS_GET_SK_URL . $_GET['sk'];

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
        $session = json_decode($result, true);


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

$data = callApi();

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
    <link rel="stylesheet" href="lib/css/bootstrap.css"  crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="lib/css/bootstrap-theme.css"  crossorigin="anonymous">
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
                    <!-- Where our message to the customer will go -->
                </p>
            </div>
        </div>
    </div>
</div>



<!-- jQuery -->
<script src="lib/js/jquery-3.2.1.js" crossorigin="anonymous"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="lib/js/bootstrap.js" crossorigin="anonymous"></script>
<script src="https://splashportal.cloud4wi.com/myapps/v1/myapps-sdk.js"></script>


<script>
    var config = <?php echo json_encode($data); ?>;

    config = typeof(config) === 'string' ? JSON.parse(config) : config;

    /**
     * "customer":{
            "lang":String,
            "is_logged":Boolean,
            "id":String,
            "first_name":String,
            "last_name":String,
            "username":String,
            "gender":String,
            "birth_date":Date "0000-00-00 00:00:00",
            "phone":String,
            "phone_prefix":String,
            "email":String,
            "mac_address":[]
        }
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

        var processedArr = arr.map(function(element) {
            element = !!object[element] ? object[element] : element;
            return element;
        });

        return processedArr.join('');
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
            tenantId:config.tenant.tenant_id,
            action:'get_messages'
        },
        success:function(data) {
            data = typeof(data) === 'string' ? JSON.parse(data) : data;

            var greetingContainer = $("#greeting");
            var message; // just in case we have to change this in the if statement

            var navbarParams = {
                fontColor:'black',
                backgroundColor:'white',
                apn:'Coffee Works'
            };

            if(data.status === 'success') {
                if(!config.customer.is_logged) {
                    navbarParams.nextBtn = 5;
                    greetingContainer.text(data.value.pre);
                }
                if(config.customer.is_logged) {
                    // Process the message to find the brackets and replace them with variables
                    message = insertMessageVariables(data.value.post, config.customer);
                    greetingContainer.text(message);
                }
            }

            MYAPPS.renderNavbar(navbarParams);
            document.body.style.marginTop = "50px";
            document.body.style.fontFamily = '"Times New Roman", "Times", "Georgia", Helvetica, Arial, sans-serif';
        },
        method:'GET'
    });
</script>
</body>
</html>