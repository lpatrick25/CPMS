@extends('layouts.base')

@section('PAGE_NAME')
    Item Request Reports
@endsection

@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            {{-- Filters --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="month">Select Month</label>
                    <select id="month" class="form-control">
                        <option value="">All</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="year">Select Year</label>
                    <select id="year" class="form-control">
                        <option value="">All</option>
                        @for ($y = date('Y'); $y >= 2000; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="employee">Select Employee</label>
                    <select id="employee" class="form-control">
                        <option value="">All</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button id="filter-btn" class="btn btn-primary w-100">Apply Filter</button>
                </div>
            </div>

            {{-- Table --}}
            <div id="toolbar"></div>
            <table id="table" data-toggle="table" data-pagination="true" data-search="true"
                data-url="{{ route('facilityReservationReport') }}" data-query-params="queryParams">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="employee_name" class="border-2">Employee Name</th>
                        <th data-field="facility_name" class="border-2">Facility Name</th>
                        <th data-field="reservation_date" class="border-2">Reservation Date</th>
                        <th data-field="reservation_time" class="border-2 rounded-end">Reservation Time</th>
                        <th data-field="status" class="border-2 rounded-end">Status</th>
                        <th data-field="facility_in_charge" class="border-2 rounded-end">In-charge</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function queryParams(params) {
            params.month = $('#month').val();
            params.year = $('#year').val();
            params.employee_id = $('#employee').val();
            return params;
        }

        $(document).ready(function() {
            // Refresh table on filter change
            $('#month, #year, #employee').change(function() {
                $('#table').bootstrapTable('refresh');
            });

            // Refresh table when button is clicked
            $('#filter-btn').click(function() {
                $('#table').bootstrapTable('refresh');
            });
        });
    </script>
@endsection
