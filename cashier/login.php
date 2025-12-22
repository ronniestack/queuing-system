<?php
session_start();

if (!empty($_SESSION['cashier_id'])) {
    header("Location: ./");
    exit;
}

require_once('./../DBConnection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Login | Cashier Queuing System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="./../css/bootstrap.min.css">
    <link rel="stylesheet" href="./../select2/css/select2.min.css">
    <script src="./../js/jquery-3.6.0.min.js"></script>
    <script src="./../js/bootstrap.min.js"></script>
    <script src="./../select2/js/select2.min.js"></script>

    <style>
        body{
            height:100vh;
            background:#f4f6f9;
        }
        .card{
            box-shadow:0 0 20px rgba(0,0,0,.08);
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">

    <div class="col-md-4">

        <h4 class="text-center mb-3">Cashier Login</h4>

        <div class="card shadow-sm">
            <div class="card-body">

                <!-- Alert -->
                <div id="alert-box" class="alert d-none"></div>

                <form id="login-form">

                    <div class="mb-3">
                        <label class="form-label">Select Cashier</label>
                        <select name="cashier_id"
                                class="form-select select2"
                                required>
                            <option value="" disabled selected>Select cashier</option>
                            <?php
                            $cashiers = $conn->query("SELECT * FROM cashier_list WHERE status = 1 ORDER BY name ASC");
                            while ($row = $cashiers->fetchArray(SQLITE3_ASSOC)):
                            ?>
                                <option value="<?= $row['cashier_id'] ?>">
                                    <?= htmlspecialchars($row['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button class="btn btn-success w-100" type="submit">
                        Login
                    </button>

                    <div class="text-center mt-3">
                        <a href="./../" class="text-success">Admin</a>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <script>
        $(function(){

            $('.select2').select2({
                width: '100%'
            });

            function showAlert(type, message, timeout = 4000) {
                const alertBox = $('#alert-box');
                alertBox
                    .removeClass('d-none alert-success alert-danger')
                    .addClass('alert-' + type)
                    .text(message)
                    .fadeIn();

                setTimeout(() => {
                    alertBox.fadeOut(() => alertBox.addClass('d-none'));
                }, timeout);
            }

            $('#login-form').on('submit', function(e){
                e.preventDefault();

                const btn = $('button[type=submit]');
                btn.prop('disabled', true).text('Logging in...');

                $.post('./../Actions.php?a=c_login', $(this).serialize(), function(resp){
                    if (resp.status === 'success') {
                        showAlert('success', 'Login successful. Redirecting...', 1200);
                        setTimeout(() => location.href = './', 1200);
                    } else {
                        showAlert('danger', resp.msg || 'Login failed.');
                        btn.prop('disabled', false).text('Login');
                    }
                }, 'json').fail(() => {
                    showAlert('danger', 'Server error.');
                    btn.prop('disabled', false).text('Login');
                });
            });

        });
    </script>
</body>
</html>