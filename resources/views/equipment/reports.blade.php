@extends('layouts.base')

@section('PAGE_NAME')
    Equipment Borrowing Reports
@endsection

@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="toolbar">
            </div>
            <table id="table"
                data-toggle="table"
                data-pagination="true"
                data-search="true"
                data-show-columns="false"
                data-show-pagination-switch="false"
                data-show-refresh="false"
                data-key-events="true"
                data-show-toggle="false"
                data-resizable="false"
                data-cookie="true"
                data-cookie-id-table="saveId"
                data-show-export="false"
                data-click-to-select="true"
                data-toolbar="#toolbar"
                data-defer-url="false"
                data-filter-control="true"
                data-fixed-columns="true"
                data-mobile-responsive="true"
                data-multiple-sort="true"
                data-page-jump-to="true"
                data-print="true"
                data-reorder-columns="true"
                data-sticky-header="true"
                data-url="{{ route('equipmentBorrowingReport') }}">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="transaction_number" class="border-2">Transaction Number</th>
                        <th data-field="employee_name" class="border-2">Employee Name</th>
                        <th data-field="equipment_name" class="border-2">Equipment Name</th>
                        <th data-field="equipment_type" class="border-2">Equipment Type</th>
                        <th data-field="borrowed_quantity" class="border-2">Borrowed Quantity</th>
                        <th data-field="serial_numbers" class="border-2">Serial Numbers</th>
                        <th data-field="status" class="border-2">Status</th>
                        <th data-field="release_date" class="border-2">Release Date</th>
                        <th data-field="return_date" class="border-2 rounded-end">Return Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize Bootstrap Table if needed
        });
    </script>
@endsection
