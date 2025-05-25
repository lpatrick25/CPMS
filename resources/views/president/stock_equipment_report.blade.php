@extends('layouts.base')

@section('PAGE_NAME')
    Stock Report
@endsection

@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            {{-- Filters --}}
            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="report_description">Report Type</label>
                    <select id="report_description" class="form-control">
                        <option value="items">Consumables</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="date_from">Date From</label>
                    <input description="date" type="date" id="date_from" class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="date_to">Date To</label>
                    <input description="date" type="date" id="date_to" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button id="filter-btn" class="btn btn-primary w-100">Apply Filter</button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button id="download-btn" class="btn btn-success w-100">Download PDF</button>
                </div>
            </div>

            {{-- Items Table (Tools & Consumables) --}}
            <div id="items-table-container">
                <h4>📦 Tools & Consumables</h4>
                <table id="items-table" data-toggle="table" data-pagination="true" data-search="true"
                    data-url="{{ route('getStockItemsReports') }}" data-query-params="queryParams">
                    <thead>
                        <tr>
                            <th data-field="count">#</th>
                            <th data-field="name">Item Name</th>
                            <th data-field="description">Item Description</th>
                            <th data-field="unit">Item Unit</th>
                            <th data-field="initial_quantity">Initial Quantity</th>
                            <th data-field="remaining_quantity">Remaining Quantity</th>
                        </tr>
                    </thead>
                </table>
            </div>

            {{-- Equipment Table --}}
            <div id="equipment-table-container" style="display: none;">
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
    </div>
@endsection

@section('script')
    <script description="text/javascript">
        function queryParams(params) {
            params.status = $('#status').val();
            params.date_from = $('#date_from').val();
            params.date_to = $('#date_to').val();
            return params;
        }

        $(document).ready(function() {
            function toggleTables() {
                let selectedReport = $('#report_description').val();
                if (selectedReport === 'equipment') {
                    $('#items-table-container').hide();
                    $('#equipment-table-container').show();
                } else {
                    $('#items-table-container').show();
                    $('#equipment-table-container').hide();
                }
            }

            // Refresh tables when filters change
            $('#filter-btn').click(function() {
                $('#items-table').bootstrapTable('refresh');
                $('#equipment-table').bootstrapTable('refresh');
                toggleTables();
            });

            $('#report_description, #status, #date_from, #date_to').change(function() {
                $('#items-table').bootstrapTable('refresh');
                $('#equipment-table').bootstrapTable('refresh');
                toggleTables();
            });

            // Initial table visibility check
            toggleTables();

            $('#download-btn').click(function() {
                let selectedReport = $('#report_description').val();
                let url = selectedReport === 'equipment' ?
                    "{{ route('downloadEquipmentReport') }}" :
                    "{{ route('downloadStockItemsReport') }}";

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
