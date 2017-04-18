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
                        <label for="pre_auth_message">Email address</label>
                        <input type="text" class="form-control" id="pre_auth_message" placeholder="i.e. Welcome to the coffee shop!">
                    </div>
                    <div class="form-group">
                        <label for="post_auth_message">Password</label>
                        <input type="text" class="form-control" id="post_auth_message" placeholder="i.e. Welcome {name} to the coffee shop!">
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.2.1.js"
        crossorigin="anonymous"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url:'/api.php',
            data: {
                action:'get_messages'
            },
            success:function(data) {
                console.log(data);
            },
            error:function(data) {
                console.log(data);
            },
            type:'POST'
        })
    });

    var form = $(document);

    form.on('submit', '#app_parameters', function(e) {
        e.preventDefault();

        var pre = $("#pre_auth_message").val();
        var post = $("#post_auth_message").val();

        $.ajax({
            url:'/api.php',
            data: {
                pre:pre,
                post:post,
                action:'set_messages'
            },
            success:function(data) {
                console.log(data);
            },
            error:function(data) {
                console.log(data);
            },
            type:'POST'
        })
    });

</script>
</body>
</html>