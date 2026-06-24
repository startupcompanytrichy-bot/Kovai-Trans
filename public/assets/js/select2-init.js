/**
 * Select2 Initialization Script
 * Handles Select2 for company forms, branch forms, vehicles, and general select2 elements.
 */

$(document).ready(function() {

    function initSelect2() {
        // Initialize general select2 elements (outside modals)
        $('.select2').each(function() {
            var $el = $(this);
            // If it is inside a modal or already initialized, skip
            if ($el.closest('.modal').length || $el.hasClass('select2-hidden-accessible')) {
                return;
            }

            $el.select2({
                width: '100%',
                placeholder: $el.data('placeholder') || 'Select option',
                allowClear: $el.data('allow-clear') || false
            });
        });

        // Company and branch form specific initializations
        var $form = $('#companyForm, #branchForm');
        if ($form.length) {
            $form.find('.select2-business-type').each(function() {
                var $el = $(this);
                $el.select2({
                    width: '100%',
                    placeholder: $el.data('placeholder') || 'Select business types...',
                    allowClear: !$el.prop('disabled'),
                    closeOnSelect: false,
                    disabled: $el.prop('disabled')
                });
            });
        }
    }

    // Run basic initialization on load
    initSelect2();

    // Automatically handle all select2 elements inside Bootstrap modals when shown
    $(document).on('shown.bs.modal', '.modal', function() {
        var $modal = $(this);
        $modal.find('.select2').each(function() {
            var $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) {
                return;
            }
            $el.select2({
                dropdownParent: $modal,
                width: '100%',
                placeholder: $el.data('placeholder') || 'Select option',
                allowClear: $el.data('allow-clear') || false
            });
        });
    });

    // Destroy select2 elements on modal hide to prevent memory leaks / duplicate elements
    $(document).on('hidden.bs.modal', '.modal', function() {
        $(this).find('.select2').each(function() {
            var $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
        });
    });
});
