<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit;
}

require_once('DBConnection.php');

$page = $_GET['page'] ?? 'home';

/* Allow only valid pages */
$allowed_pages = ['home','users','cashiers','manage_account'];
if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= ucwords(str_replace('_',' ',$page)) ?> | Cashier Queuing System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./assets/favicon.png">

    <!-- CSS -->
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./select2/css/select2.min.css">
    <link rel="stylesheet" href="./DataTables/datatables.min.css">
    <link rel="stylesheet" href="./FontAwesome/css/all.min.css">

    <!-- JS -->
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./select2/js/select2.min.js"></script>
    <script src="./DataTables/datatables.min.js"></script>
    <script src="./js/sweetalert2.all.min.js"></script>
    <script src="./js/script.js"></script>

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
            width: auto;
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #198754;
        }

        .nav-link.active::after {
            width: 100%;
        }

        /* Alerts */
        .dynamic_alert {
            border-radius: .25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #adb5bd;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <main>
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm" id="topNavBar">
            <div class="container-fluid px-4">

                <!-- LOGO BRAND -->
                <a class="navbar-brand d-flex align-items-center" href="./">
                    <img src="./assets/logo.png" alt="Queuing Logo" class="me-2">
                    <span class="fw-bold text-success">Queuing</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- MENU -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= $page=='home'?'active':'' ?>" href="./">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page=='users'?'active':'' ?>" href="./?page=users">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page=='cashiers'?'active':'' ?>" href="./?page=cashiers">Cashiers</a>
                        </li>
                    </ul>

                    <!-- USER DROPDOWN -->
                    <div class="dropdown">
                        <button class="btn btn-link text-success dropdown-toggle text-decoration-none"
                                data-bs-toggle="dropdown">
                            Hello <?= htmlspecialchars($_SESSION['fullname']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <a class="dropdown-item" href="./?page=manage_account">
                                    <i class="fa fa-user-cog me-1"></i> Manage Account
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="./Actions.php?a=logout">
                                    <i class="fa fa-sign-out-alt me-1"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- PAGE CONTENT -->
        <div class="container py-4" id="page-container">

            <?php if (isset($_SESSION['flashdata'])): ?>
                <div class="alert alert-<?= $_SESSION['flashdata']['type'] ?> dynamic_alert">
                    <?= $_SESSION['flashdata']['msg'] ?>
                </div>
                <?php unset($_SESSION['flashdata']); ?>
            <?php endif; ?>

            <?php include $page . '.php'; ?>

        </div>
    </main>

    <!-- UNIVERSAL MODALS -->
    <div class="modal fade" id="uni_modal" data-bs-backdrop="static">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer py-1">
                    <button class="btn btn-primary btn-sm" onclick="$('#uni_modal form').submit()">Save</button>
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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
                    <button class="btn btn-danger btn-sm" id="confirm">Continue</button>
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
