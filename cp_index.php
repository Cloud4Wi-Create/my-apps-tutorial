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
                    <p class="bg-success hide" id="api_status_message">Success</p>
                    <p class="bg-danger hide" id="api_failure_message">Failure</p>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.2.1.js" crossorigin="anonymous"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(document).ready(function() {
        /**
         * To check and see if there are any messages already stored
         * in order to pre-populate inputs
         */
        $.ajax({
            url:'/api.php',
            data: {
                action:'get_messages'
            },
            success:function(data) {
                console.log(data);
                data = typeof(data) === 'string' ? JSON.parse(data) : data;

                var preAuthMessageInput = $("#pre_auth_message");
                var postAuthMessageInput = $("#pre_auth_message");

                if(data.status === 'success') {
                    if(!!data.status.value.pre) {
                        preAuthMessageInput.val(data.status.value.pre);
                    }
                    if(!!data.status.value.post) {
                        postAuthMessageInput.val(data.status.value.post);
                    }
                }
            },
            error:function(data) {
                console.log(data);
            },
            method:'GET'
        })
    });

    var form = $(document);

    form.on('submit', '#app_parameters', function(e) {
        e.preventDefault();

        var preAuthMessage = $("#pre_auth_message").val();
        var postAuthMessage = $("#post_auth_message").val();
        var apiSuccessMessage = $("#api_success_message");
        var apiFailureMessage = $("#api_failure_message");

        var postObject = $.post( "/api.php", {
                pre:preAuthMessage,
                post:postAuthMessage,
                action:'set_messages'
            }, function() {
                console.log(arguments);
                console.log("Success");
            });

        console.log(postObject);

//        $.ajax({
//            url:'/api.php',
//            data: {
//                pre:pre,
//                post:post,
//                action:'set_messages'
//            },
//            success:function(data) {
//                console.log(data);
//            },
//            error:function(data) {
//                console.log(data);
//            },
//            method:'GET'
//        })
    });

</script>
</body>
</html>