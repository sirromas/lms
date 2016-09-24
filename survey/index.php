
<!DOCTYPE html>

<html>
    <head>
        <title>GLOBALIZATION PLUS</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >
     
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    </head>
  <body>
        <br>
        <div class="panel panel-default" style="width:640px;margin: auto;">
            <div class="panel-heading"><div class='text-center'>Authorization</div></div>
            <div class="panel-body">
                <form action="mailer.php" method="post" id="login" name="login">
                    <div class="form-group">
                        <label for="login">Username*</label>
                        <input type="text" class="form-control" id="username" required name="username" placeholder="Enter Username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password*</label>
                        <input type="password" class="form-control" id="password" required name="password" placeholder="Enter Password">
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>


    </body>
</html>
