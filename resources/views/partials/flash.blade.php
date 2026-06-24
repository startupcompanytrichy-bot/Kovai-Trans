{{--
    Flash messages — rendered as toastr toasts (top-right, professional style).
    Toastr JS/CSS is loaded in partials/footer.blade.php.
    This partial just emits the trigger script; no visible HTML elements.
--}}
@php
    $flashTypes = [
        'success' => ['success', 'ti-check-box',    'Success'],
        'error'   => ['error',   'ti-close',         'Error'],
        'warning' => ['warning', 'ti-alert',         'Warning'],
        'info'    => ['info',    'ti-info-alt',       'Info'],
    ];
@endphp

@foreach ($flashTypes as $sessionKey => [$toastrMethod, $icon, $title])
    @if (session($sessionKey))
    <script>
    (function waitForToastr() {
        if (typeof toastr === 'undefined') {
            return setTimeout(waitForToastr, 50);
        }
        toastr['{{ $toastrMethod }}'](
            {!! json_encode(session($sessionKey)) !!},
            '{{ $title }}'
        );
    })();
    </script>
    @endif
@endforeach
