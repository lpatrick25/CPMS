@extends('layouts.base')

@section('PAGE_NAME')
    Stock & Equipment Report
@endsection

@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            {{-- Filters --}}
            <div class="row mb-3">

                <div class="col-md-4">
                    <label for="status">Equipment Status</label>
                    <select id="status" class="form-control">
                        <option value="">All</option>
                        <option value="Available">Available</option>
                        <option value="Borrowed">Borrowed</option>
                        <option value="Under Maintenance">Under Maintenance</option>
                        <option value="Retired">Retired</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from">Date From</label>
                    <input type="date" id="date_from" class="form-control">
                </div>

                <div class="col-md-2">
                    <label for="date_to">Date To</label>
                    <input type="date" id="date_to" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button id="filter-btn" class="btn btn-primary w-100">Apply Filter</button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button id="download-btn" class="btn btn-success w-100">Download PDF</button>
                </div>
            </div>

            <h4>🛠 Equipment</h4>
            <table id="equipment-table" data-toggle="table" data-pagination="true" data-search="true"
                data-url="{{ route('getEquipmentReports') }}" data-query-params="queryParams">
                <thead>
                    <tr>
                        <th data-field="count">#</th>
                        <th data-field="name">Equipment Name</th>
                        <th data-field="initial_quantity">Initial Quantity</th>
                        <th data-field="remaining_quantity">Remaining Quantity</th>
                        <th data-field="available_serials">Available</th>
                        <th data-field="borrowed_serials">Borrowed</th>
                        <th data-field="under_maintenance">Under Maintenance</th>
                        <th data-field="retired_serials">Retired</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function queryParams(params) {
            params.type = $('#type').val();
            params.status = $('#status').val();
            params.date_from = $('#date_from').val();
            params.date_to = $('#date_to').val();
            return params;
        }

        $(document).ready(function() {
            // Refresh tables when filters change
            $('#filter-btn').click(function() {
                $('#equipment-table').bootstrapTable('refresh');
            });

            $('#type, #status, #date_from, #date_to').change(function() {
                $('#equipment-table').bootstrapTable('refresh');
            });

            $('#download-btn').click(function() {
                let selectedReport = $('#report_type').val();
                let url = "{{ route('downloadEquipmentReport') }}";

                let params = new URLSearchParams({
                    status: $('#status').val(),
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                });

                window.location.href = url + '?' + params.toString();
            });
        });
    </script>
@endsection
