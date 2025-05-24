@extends('layouts.base')
@section('PAGE_NAME')
    Facilities
@endsection
@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="toolbar">

            </div>
            <table id="table" data-toggle="table" data-pagination="true" data-search="true" data-show-columns="false"
                data-show-pagination-switch="false" data-show-refresh="false" data-key-events="true" data-show-toggle="false"
                data-resizable="false" data-cookie="true" data-cookie-id-table="saveId" data-show-export="false"
                data-click-to-select="true" data-toolbar="#toolbar" data-defer-url="false" data-filter-control="true"
                data-fixed-columns="true" data-mobile-responsive="true" data-multiple-sort="true" data-page-jump-to="true"
                data-print="true" data-reorder-columns="true" data-sticky-header="true" data-url="/facilities">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="facility_name" class="border-2">Facility Name</th>
                        <th data-field="facility_description" class="border-2">Facility Description</th>
                        <th data-field="facility_status" class="border-2">Facility Status</th>
                        <th data-field="facility_in_charge" class="border-2">Facility In-charge)</th>
                        <th data-field="action" class="border-2 rounded-end">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function view(facility_id) {
            $.ajax({
                method: 'GET',
                url: `/facilities/${facility_id}`,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response) {
                        window.location.href = `/employee/facilitiesReservation/${response.id}`;
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                        showErrorMessage(jqXHR.responseJSON
                            .msg); // Show general error message
                    } else {
                        showErrorMessage(
                            "An unexpected error occurred. Please try again."); // Generic error message
                    }
                }

            });
        }

        $(document).ready(function() {



        });
    </script>
@endsection
