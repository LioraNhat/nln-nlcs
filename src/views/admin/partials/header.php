<?php // File: src/views/admin/partials/header.php ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?? 'Admin' ?></title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/jqvmap/jqvmap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>dist/css/adminlte.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>dist/css/custom.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <script src="<?php echo BASE_URL ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo BASE_URL ?>plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?php echo BASE_URL ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo BASE_URL ?>plugins/toastr/toastr.min.js"></script>
    <script>
        // Định nghĩa _base_url_ cho các file JS cũ
        var _base_url_ = '<?php echo BASE_URL ?>';
    </script>
    </head>