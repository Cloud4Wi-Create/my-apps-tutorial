<?php

$getSessionDataUrl = C4W_ENV_CONTROLPANEL_URL . C4W_ENV_MYAPPS_GET_SK_URL . $_GET['sk'];

?>

<?php

/**
 * @description: This function will not take any parameters,
 * it will just call the C4W API and return the data.  If the
 * SK is not set in the URL or if the API call is not a successful one,
 * then it will return a false boolean. Otherwise it will return the object
 *
 * @returns { Object | Boolean };
 */

function callApi() {
    $sk = $_GET['sk'];

    // Check to see if any of the places where SK is set exists
    if(isset($sk) && !empty($sk)) {

        // Concatenate URL
        $url = C4W_ENV_CONTROLPANEL_URL . C4W_ENV_MYAPPS_GET_SK_URL . $sk;

        // Call C4W API
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

<script type="text/javascript">
    // For use in JavaScript
    var config = <?php echo json_encode(callApi()); ?>;
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
<div class="container">
    <div class="row">
        <div class="box">
            <div class="col-lg-12">
                <hr>
                <h2 class="intro-text text-center">Parameters</h2>
                <hr>
                <hr class="visible-xs">
                <form id="app_parameters">
                    <div class="form-group">
                        <label for="pre_auth_message">Pre Auth Message</label>
                        <input type="text" class="form-control" id="pre_auth_message" placeholder="i.e. Welcome to the coffee shop!">
                    </div>
                    <div class="form-group">
                        <label for="post_auth_message">Post Auth Message</label>
                        <input type="text" class="form-control" id="post_auth_message" placeholder="i.e. Welcome {name} to the coffee shop!">
                    </div>
                    <p class="bg-success hide" id="api_success_message">Success</p>
                    <p class="bg-danger hide" id="api_failure_message">Failure</p>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="lib/js/jquery-3.2.1.js" crossorigin="anonymous"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="lib/js/bootstrap.js" crossorigin="anonymous"></script>

<script type="text/javascript">
    /**
     * This is where we make calls to the third-party
     * database
     */
    $(document).ready(function() {
        /**
         * To check and see if there are any messages already stored
         * in order to pre-populate inputs.
         *
         * If there are values for either the pre or post authentication
         * messages, then it will populate them individually. One
         * does not affect the other.
         */
        $.ajax({
            url:'api.php',
            data: {
                tenantId:config.auth.tenantId,
                action:'get_messages'
            },
            success:function(data) {
                data = typeof(data) === 'string' ? JSON.parse(data) : data;

                var preAuthMessageInput = $("#pre_auth_message");
                var postAuthMessageInput = $("#post_auth_message");

                if(data.status === 'success') {
                    if(!!data.value.pre) {
                        preAuthMessageInput.val(data.value.pre);
                    }
                    if(!!data.value.post) {
                        postAuthMessageInput.val(data.value.post);
                    }
                }
            },
            method:'GET'
        })
    });

    var form = $(document);

    /**
     * @description
     * On form submit, this will take variables "preAuthMessage" and "postAuthMessage"
     * along with "tenantId", and send them to the API.
     *
     * In case of any error, it will show the error message until the next time
     * you submit the data.
     *
     * In case of success, it will show the success message for 3 seconds and then
     * hide it again.
     *
     * This is just a quick GET call to send data.
     */
    form.on('submit', '#app_parameters', function(e) {
        e.preventDefault();

        var preAuthMessage = $("#pre_auth_message").val();
        var postAuthMessage = $("#post_auth_message").val();
        var apiSuccessMessage = $("#api_success_message");
        var apiFailureMessage = $("#api_failure_message");

        if(!apiFailureMessage.hasClass('hide')) apiFailureMessage.addClass('hide');

        $.ajax({
            url:'api.php',
            data: {
                tenantId:config.auth.tenantId,
                pre:preAuthMessage,
                post:postAuthMessage,
                action:'set_messages'
            },
            success:function(data) {
                data = typeof(data) === 'string' ? JSON.parse(data) : data;

                if(data.status === 'success') {
                    apiSuccessMessage.removeClass('hide');

                    setTimeout(function() {
                        apiSuccessMessage.addClass('hide');
                    }, 3000);
                }
                if(data.status === 'error') {
                    apiFailureMessage.removeClass('hide');
                }
            },
            error:function() {
                apiFailureMessage.removeClass('hide');
            },
            method:'GET'
        })
    });

</script>
</body>
</html>