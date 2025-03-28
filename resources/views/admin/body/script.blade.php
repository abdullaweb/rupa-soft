<!-- JAVASCRIPT -->
<script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>


<!-- apexcharts -->
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- jquery.vectormap map -->
<script src="{{ asset('backend/assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') }}">
</script>
<script src="{{ asset('backend/assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js') }}">
</script>

<!-- select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<!-- App js -->
<script src="{{ asset('backend/assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/pages/form-validation.init.js') }}"></script>
<script src="{{ asset('backend/assets/js/app.js') }}"></script>
<script src="{{ asset('backend/assets/js/code.js') }}"></script>
<script src="{{ asset('backend/assets/js/handlebars.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ asset('backend/assets/js/validate.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('backend/assets/js/validate.min.js') }}"></script>

<script>
    @if (Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}"
        switch (type) {
            case 'info':
                toastr.info(" {{ Session::get('message') }} ");
                break;

            case 'success':
                toastr.success(" {{ Session::get('message') }} ");
                break;

            case 'warning':
                toastr.warning(" {{ Session::get('message') }} ");
                break;

            case 'error':
                toastr.error(" {{ Session::get('message') }} ");
                break;
        }
    @endif
</script>

{{-- date picker --}}
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<script>
    $(function() {
        $(".date_picker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
        });
        
         $('.select2').select2();
    });
</script>

<script>
    $(document).ready(function() {
        // Initialize Select2 with search functionality
        $('#invoice').select2({
            placeholder: "Search or select invoice", // Placeholder text
            allowClear: true, // Option to clear the selection
            width: '100%', // Make the dropdown full width
            multiple: true
        });
    });
</script>

<script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>

<!-- Required datatable js -->
<script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Buttons examples -->
{{-- <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
        <script src="{{ asset('backend/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
        <script src="{{ asset('backend/assets/libs/jszip/jszip.min.js')}}"></script>
        <script src="{{ asset('backend/assets/libs/pdfmake/build/pdfmake.min.js')}}"></script>
        <script src="{{ asset('backend/assets/libs/pdfmake/build/vfs_fonts.js')}}"></script>
        <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
        <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
        <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script> --}}

{{-- <script src="{{ asset('backend/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js')}}"></script>
        <script src="{{ asset('backend/assets/libs/datatables.net-select/js/dataTables.select.min.js')}}"></script> --}}

<!-- Responsive examples -->
<script src="{{ asset('backend/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}">
</script>

<!-- Datatable init js -->
<script src="{{ asset('backend/assets/js/pages/datatables.init.js') }}"></script>
<script>
$(document).ready(function() {
    // Check if DataTable is already initialized
    if ($.fn.dataTable.isDataTable('#datatable2')) {
        $('#datatable2').DataTable().destroy(); // Destroy the existing instance
    }

    // Initialize DataTable with pagination disabled
    var table = $('#datatable2').DataTable({
        pageLength: -1,
        "lengthMenu": [
            [100, 5000, 10000, -1],
            [100, 5000, 10000, "All"]
        ],
    });
});
</script>
