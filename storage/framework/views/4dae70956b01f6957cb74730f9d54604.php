<!DOCTYPE html>
<html lang="en">

<head>
    <title>Transport Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="CodedThemes">
    <meta name="keywords" content=" Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="CodedThemes">
    <link rel="icon" href="<?php echo e(asset('assets/images/favicon.ico')); ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/bootstrap/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/icon/themify-icons/themify-icons.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/icon/icofont/css/icofont.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/jquery.mCustomScrollbar.css')); ?>">
    
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/select2/select2.min.css')); ?>">
    
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/datatable/dataTables.bootstrap4.min.css')); ?>">
    
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/toastr/toastr.min.css')); ?>">
    
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/select2-theme.css')); ?>?v=<?php echo e(filemtime(public_path('assets/css/select2-theme.css'))); ?>">
    
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/app-custom.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
    <style>
        /* ── Remove tick/checkmark on selected option in Select2 dropdown ── */
    .select2-results__option[aria-selected="true"]::before,
    .select2-results__option--selected::before {
        display: none !important;
    }
    .select2-results__option[aria-selected="true"] {
        background-color: #f0f4ff !important;
        color: #1a2340 !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #667eea !important;
        color: #fff !important;
    }
    .select2-results__option--highlighted[aria-selected] span,
    .select2-results__option--highlighted[aria-selected] div {
        color: #fff !important;
    }
    </style>
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->

    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            
            <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">

                    
                    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <div class="pcoded-content">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>

                </div>
            </div>

            
            <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('partials.delete-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>
    </div>

    

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        $(document).ready(function() {
            if (typeof initSelect2Events === 'function') {
                initSelect2Events();
            }

            // Keep submenu open for active parent — pcoded uses pcoded-trigger
            $('.pcoded-hasmenu.pcoded-trigger').each(function() {
                $(this).find('> .pcoded-submenu').css('display', 'block');
            });
        });
    </script>

</body>

</html><?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/layouts/app.blade.php ENDPATH**/ ?>