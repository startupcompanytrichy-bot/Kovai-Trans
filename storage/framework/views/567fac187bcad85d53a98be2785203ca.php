<!--[if lt IE 9]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade to any of the following web browsers.</p>
</div>
<![endif]-->

<!-- Core JS (jQuery must be first) -->
<script type="text/javascript" src="<?php echo e(asset('assets/js/jquery/jquery.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-ui/jquery-ui.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/js/popper.js/popper.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/js/bootstrap/js/bootstrap.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-slimscroll/jquery.slimscroll.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/js/modernizr/modernizr.js')); ?>"></script>
<script src="<?php echo e(asset('assets/pages/widget/amchart/amcharts.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/pages/widget/amchart/serial.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/pages/todo/todo.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/pages/dashboard/custom-dashboard.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/js/script.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/js/SmoothScroll.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/pcoded.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/demo-12.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/jquery.mCustomScrollbar.concat.min.js')); ?>"></script>

<!-- Scroll-to-top button -->
<script>
    var $window = $(window);
    var nav = $('.fixed-button');
    $window.scroll(function() {
        if ($window.scrollTop() >= 200) {
            nav.addClass('active');
        } else {
            nav.removeClass('active');
        }
    });
    
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );

    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<!-- Plugin JS: Select2, DataTables, Toastr -->
<script src="<?php echo e(asset('assets/js/select2/select2.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/datatable/dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/datatable/dataTables.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/toastr/toastr.min.js')); ?>"></script>

<script>
    // ── Toastr global configuration ─────────────────────────────────────────
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "200",
        "hideDuration": "400",
        "timeOut": "4500",
        "extendedTimeOut": "1200",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };
</script>

<script>
    // Global Select2 initializer: keeps basic Select2 fields ready without clobbering page-specific setup.
    (function() {
        function initSelect2Field($el, options) {
            if ($el.hasClass('select2-hidden-accessible')) return;

            $el.select2($.extend({
                width: '100%',
                allowClear: true,
                placeholder: $el.data('placeholder') || $el.find('option:first').text()
            }, options || {}));
        }

        function initAllSelect2() {
            if (!window.jQuery || !$.fn.select2) return;

            $('.select2').each(function() {
                var $el = $(this);
                if ($el.closest('.modal').length) return;
                initSelect2Field($el);
            });
        }

        $(document).ready(function() {
            initAllSelect2();
            window.reinitSelect2 = initAllSelect2;
        });

        $(document).on('shown.bs.modal', '.modal', function() {
            var $modal = $(this);
            $modal.find('.select2').each(function() {
                initSelect2Field($(this), {
                    dropdownParent: $modal
                });
            });
        });

        $(document).on('hidden.bs.modal', '.modal', function() {
            $(this).find('.select2').each(function() {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
            });
        });
    })();
</script>
<?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/partials/footer.blade.php ENDPATH**/ ?>