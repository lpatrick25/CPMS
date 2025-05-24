@extends('layouts.base')
@section('PAGE_NAME')
    Equipment Requests
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
                data-print="true" data-reorder-columns="true" data-sticky-header="true" data-url="/equipmentRequests">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="transaction_number" class="border-2">Transaction No</th>
                        <th data-field="equipment_name" class="border-2">Equipment Name</th>
                        <th data-field="quantity" class="border-2">Quantity</th>
                        <th data-field="date_of_usage" class="border-2">Date of Usage</th>
                        <th data-field="date_of_return" class="border-2">Date of Return</th>
                        <th data-field="employee_name" class="border-2">Employee</th>
                        <th data-field="status" class="border-2">Status</th>
                        <th data-field="action" class="border-2 rounded-end">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Update Equipment Request Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="updateForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Update @yield('PAGE_NAME')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="equipment_name">Equipment Name: <span class="text-danger">*</span></label>
                        <input type="text" name="equipment_name" id="equipment_name" class="form-control" disabled>
                    </div>
                    <div class="form-group mb-4">
                        <label for="quantity">Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="release_quantity">Release Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="release_quantity" id="release_quantity" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                    <button type="button" class="btn btn-primary text-white-600" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        let requestID;

        function approve(request_id) {
            $.ajax({
                method: 'PUT',
                url: `/equipment/updateBorrowingStatus/${request_id}`,
                data: {
                    status: 'President Approval'
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                        showErrorMessage(jqXHR.responseJSON.msg);
                    } else {
                        showErrorMessage("An unexpected error occurred. Please try again.");
                    }
                }
            });
        }

        function reject(request_id) {
            $.ajax({
                method: 'PUT',
                url: `/equipment/updateBorrowingStatus/${request_id}`,
                data: {
                    status: 'Rejected'
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                        showErrorMessage(jqXHR.responseJSON.msg);
                    } else {
                        showErrorMessage("An unexpected error occurred. Please try again.");
                    }
                }
            });
        }

        function release(request_id) {
            $.ajax({
                method: 'PUT',
                url: `/equipment/updateBorrowingStatus/${request_id}`,
                data: {
                    status: 'Released',
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                        $('#updateModal').modal('hide');
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                        showErrorMessage(jqXHR.responseJSON.msg);
                    } else {
                        showErrorMessage("An unexpected error occurred. Please try again.");
                    }
                }
            });
            // $.ajax({
            //     method: 'GET',
            //     url: `/equipmentRequests/${request_id}`,
            //     dataType: 'JSON',
            //     cache: false,
            //     success: function(response) {
            //         if (response) {
            //             requestID = request_id;
            //             $('#equipment_name').val(response.equipment.name);
            //             $('#quantity').val(response.quantity);
            //             $('#updateModal').modal({
            //                 backdrop: 'static',
            //                 keyboard: false,
            //             }).modal('show');
            //         }
            //     },
            //     error: function(jqXHR) {
            //         if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
            //             showErrorMessage(jqXHR.responseJSON.msg);
            //         } else {
            //             showErrorMessage("An unexpected error occurred. Please try again.");
            //         }
            //     }
            // });
        }

        $(document).ready(function() {

            $('#updateForm').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    method: 'PUT',
                    url: `/equipment/updateBorrowingStatus/${requestID}`,
                    data: {
                        status: 'Released',
                        release_quantity: $('#release_quantity').val()
                    },
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#table').bootstrapTable('refresh');
                            showSuccessMessage(response.msg);
                            $('#updateModal').modal('hide');
                        }
                    },
                    error: function(jqXHR) {
                        if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                            showErrorMessage(jqXHR.responseJSON.msg);
                        } else {
                            showErrorMessage("An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });
        });
    </script>
@endsection
