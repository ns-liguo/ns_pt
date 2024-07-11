<?php
include "layouts/session.php";
?>
<?php include "layouts/main.php"; ?>

<head>
    <?php includeFileWithVariables(
        "layouts/title-meta.php",
        ["title" => ""]
    ); ?>
    <?php include "layouts/head-css.php"; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php include "layouts/menu.php"; ?>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    
                </div>
            </div>
            <?php include "layouts/footer.php"; ?>
        </div>
    </div>
    <?php include "layouts/vendor-scripts.php"; ?>
    <script src="assets/js/app.js"></script>
    
</body>

</html>