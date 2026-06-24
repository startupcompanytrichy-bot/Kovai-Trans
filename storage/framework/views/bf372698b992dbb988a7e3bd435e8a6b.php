
<?php
    $flashTypes = [
        'success' => ['success', 'ti-check-box',    'Success'],
        'error'   => ['error',   'ti-close',         'Error'],
        'warning' => ['warning', 'ti-alert',         'Warning'],
        'info'    => ['info',    'ti-info-alt',       'Info'],
    ];
?>

<?php $__currentLoopData = $flashTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sessionKey => [$toastrMethod, $icon, $title]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(session($sessionKey)): ?>
    <script>
    (function waitForToastr() {
        if (typeof toastr === 'undefined') {
            return setTimeout(waitForToastr, 50);
        }
        toastr['<?php echo e($toastrMethod); ?>'](
            <?php echo json_encode(session($sessionKey)); ?>,
            '<?php echo e($title); ?>'
        );
    })();
    </script>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/partials/flash.blade.php ENDPATH**/ ?>