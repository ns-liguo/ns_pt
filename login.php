<?php include 'layouts/main.php'; ?>
<head>
    <?php includeFileWithVariables('layouts/title-meta.php', array('title' => 'Sign In')); ?>
    <?php include 'layouts/head-css.php'; ?>
</head>
<body>
    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <!-- end col -->
                                <div class="">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p class="text-muted">Sign in to continue to Newstars.</p>
                                        </div>
                                        <div class="mt-4">
                                            <form method="post" action="api/login.php">
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Username</label>
                                                    <input type="text" class="form-control" name="username"
                                                        id="username" placeholder="Enter username">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="password-input">Password</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" name="password"
                                                            class="form-control pe-5 password-input"
                                                            placeholder="Enter password" id="password-input">
                                                        <button
                                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                            type="button" id="password-addon"><i
                                                                class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>

                                                <!-- <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                                </div> -->

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Log In</button>
                                                </div>


                                            </form>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Toggle Between Modals -->

    <!-- First modal dialog -->
    <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop"
                        colors="primary:#f7b84b,secondary:#405189" style="width:130px;height:130px">
                    </lord-icon>
                    <div class="mt-4 pt-4">
                        <h4>Uh oh, something went wrong!</h4>
                        <p class="text-muted"> The transfer was not successfully received by us. the email of the
                            recipient wasn't correct.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>

    <script src="https://assets.newstarsec.com/theme/velzon/assets/js/pages/password-addon.init.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let urlParams = new URLSearchParams(window.location.search);
            let error = urlParams.get('error');

            if (error) {
                // 根据错误类型设置模态框文本
                let message, title;
                switch (error) {
                    case 'usernotfound':
                        title = '账号不存在';
                        message = '不存在的账号，请您检查后重新输入。';
                        break;
                    case 'passworderror':
                        title = '密码错误';
                        message = '密码错误，请您检查后重新输入。';
                        break;
                    case 'roleerror':
                        title = '访问受限';
                        message = '您无权限访问，请联系网站管理员。';
                        break;
                    default:
                        title = '错误';
                        message = '发生未知错误，请重试。';
                        break;
                }

                // 设置模态框中的文本
                document.querySelector('#firstmodal .modal-body h4').textContent = title;
                document.querySelector('#firstmodal .modal-body p').textContent = message;

                // 显示模态框
                let myModal = new bootstrap.Modal(document.getElementById('firstmodal'), {
                    keyboard: false // 可选：禁止键盘关闭模态框
                });
                myModal.show();

                // 当模态框关闭时重定向
                document.getElementById('firstmodal').addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'login.php';
                });
            }
        });



    </script>
</body>

</html>