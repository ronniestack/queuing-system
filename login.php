<?php
    session_start();
    if (!empty($_SESSION['user_id'])) {
        header("Location: ./");
        exit;
    }
    require_once('DBConnection.php');
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Cashier Queuing System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <style>
        body{
            height:100vh;
            background:#121212
        }
        .card{
            box-shadow:0 0 25px rgba(0,0,0,.6)
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="col-md-4">
        <h4 class="text-center text-light mb-4">FMMC Queuing System</h4>

        <div class="card rounded-0">
            <div class="card-body">
                <form id="login-form">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePass">👁</button>
                        </div>
                    </div>

                    <button class="btn btn-success w-100" type="submit">
                        Login
                    </button>

                    <div class="text-center mt-3">
                        <a href="./queue_registration.php" class="text-success">Home</a> |
                        <a href="./cashier" class="text-success">Cashier</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('#togglePass').click(()=> {
            $('#password').attr('type',
                $('#password').attr('type') === 'password' ? 'text' : 'password'
            );
        });

        $('#login-form').submit(function(e){
            e.preventDefault();
            const btn = $('button[type=submit]');
            btn.prop('disabled',true).text('Logging in...');

            $.post('./Actions.php?a=login', $(this).serialize(), function(resp){
                if(resp.status === 'success'){
                    location.href = './';
                } else {
                    alert(resp.msg);
                    btn.prop('disabled',false).text('Login');
                }
            }, 'json');
        });
    </script>
</body>
</html>
