<?php
session_start();
require_once('DBConnection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./FontAwesome/css/all.min.css">

    <!-- JS -->
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>

    <style>
        html, body {
            height: 100%;
            background: #f8f9fa;
        }

        .navbar-brand img {
            height: 40px;
        }

        .queue-card {
            max-width: 480px;
            margin: auto;
        }

        .form-control-lg {
            font-size: 1.25rem;
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-light bg-white border-bottom shadow-sm">
        <div class="container justify-content-center">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="./assets/logo.png" class="me-2" alt="Logo">
                <span class="fw-semibold text-success">Queue Registration</span>
            </a>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="container d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 80px);">
        <div class="queue-card w-100">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h5 class="text-center mb-4 fw-semibold">
                        Get Your Queue Number
                    </h5>

                    <form id="queue-form">
                        <div class="mb-4">
                            <label class="form-label text-muted">
                                Enter your full name
                            </label>
                            <input type="text"
                                name="customer_name"
                                class="form-control form-control-lg text-center"
                                autocomplete="off"
                                required
                                autofocus>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-success btn-lg" type="submit">
                                <i class="fa-solid fa-ticket me-2"></i>
                                Get Queue Number
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <!-- MODALS (UNCHANGED) -->
    <div class="modal fade" id="uni_modal" data-bs-backdrop="static">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer py-1">
                    <button class="btn btn-sm btn-primary" onclick="$('#uni_modal form').submit()">Save</button>
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function(){

            $('#queue-form').on('submit', function(e){
                e.preventDefault();

                const form = $(this);
                const btn = form.find('button[type="submit"]');
                btn.prop('disabled', true).text('Please wait...');

                $.ajax({
                    url: './Actions.php?a=save_queue',
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(resp){

                        if (resp.status === 'success') {
                            uni_modal(
                                'Your Queue Number',
                                'get_queue.php?success=true&id=' + resp.id
                            );

                            $('#uni_modal').on('hidden.bs.modal', function () {
                                location.reload();
                            });
                        } else {
                            alert(resp.msg || 'Unable to get queue.');
                        }

                        btn.prop('disabled', false).text('Get Queue Number');
                    },
                    error: function(){
                        alert('Server error. Please try again.');
                        btn.prop('disabled', false).text('Get Queue Number');
                    }
                });
            });

        });
    </script>

</body>
</html>
