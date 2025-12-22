<?php 
session_start();
if(!isset($_SESSION['cashier_id'])){
    header("Location:./login.php");
    exit;
}
require_once('./../DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= ucwords(str_replace('_',' ',$page)) ?> | Cashier Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./../assets/favicon.png">

    <!-- CSS -->
    <link rel="stylesheet" href="./../css/bootstrap.min.css">
    <link rel="stylesheet" href="./../select2/css/select2.min.css">
    <link rel="stylesheet" href="./../DataTables/datatables.min.css">
    <link rel="stylesheet" href="./../FontAwesome/css/all.min.css">

    <!-- JS -->
    <script src="./../js/jquery-3.6.0.min.js"></script>
    <script src="./../js/bootstrap.min.js"></script>
    <script src="./../select2/js/select2.min.js"></script>
    <script src="./../DataTables/datatables.min.js"></script>
    <script src="./../js/sweetalert2.all.min.js"></script>
    <script src="./../js/script.js"></script>

    <style>
        html, body {
            height: 100%;
            background: #f8f9fa;
        }

        main {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        #page-container {
            flex: 1;
            overflow-y: auto;
        }

        /* Navbar */
        #topNavBar {
            background: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }

        .navbar-brand img {
            height: 40px;
        }

        /* Scrollbar (subtle) */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #adb5bd;
            border-radius: 10px;
        }

        .dynamic_alert {
            border-radius: .25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
        }
    </style>
</head>

<body>
    <main>
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm" id="topNavBar">
            <div class="container">

                <!-- LOGO -->
                <a class="navbar-brand d-flex align-items-center" href="./">
                    <img src="./../assets/logo.png" alt="Logo" class="me-2">
                    <span class="fw-semibold text-success">Cashier Panel</span>
                </a>

                <!-- USER DROPDOWN -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user"></i>
                        <?= htmlspecialchars($_SESSION['name']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item text-danger"
                            href="./../Actions.php?a=c_logout">
                                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        <!-- PAGE CONTENT -->
        <div class="container py-3" id="page-container">

            <?php if(isset($_SESSION['flashdata'])): ?>
                <div class="dynamic_alert alert alert-<?= $_SESSION['flashdata']['type'] ?>">
                    <?= $_SESSION['flashdata']['msg'] ?>
                </div>
                <?php unset($_SESSION['flashdata']); ?>
            <?php endif; ?>

            <?php include $page . '.php'; ?>

        </div>
    </main>

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

    <div class="modal fade" id="confirm_modal">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title">Confirmation</h5>
                </div>
                <div class="modal-body" id="delete_content"></div>
                <div class="modal-footer py-1">
                    <button class="btn btn-sm btn-primary" id="confirm">Continue</button>
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
