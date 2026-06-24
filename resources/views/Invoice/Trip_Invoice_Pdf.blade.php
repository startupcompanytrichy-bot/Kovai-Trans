@include('Invoice.Trip_Invoice')
<script>
window.onload = function () {
    document.title = '{{ addslashes($_pageTitle) }}';
    window.print();
};
</script>
