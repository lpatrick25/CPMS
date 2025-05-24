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
    <div class="modal fade" id="serialsModal" tabindex="-1" aria-labelledby="serialsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Serials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="serialsList">
                    <!-- Serials will be loaded here dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="submitSerialSelection()">Confirm
                        Selection</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function approve(request_id) {
            updateStatus(request_id, 'President Approval');
        }

        function reject(request_id) {
            updateStatus(request_id, 'Rejected');
        }

        function release(request_id, has_serial, quantity) {
            if (has_serial) {
                $('#serialsModal').data('request-id', request_id).data('quantity', quantity).modal('show');
            } else {
                releaseNonSerialized(request_id);
            }
        }

        function releaseNonSerialized(request_id) {
            updateStatus(request_id, 'Released');
        }

        function markReturned(request_id) {
            updateStatus(request_id, 'Returned');
        }

        function updateStatus(request_id, status) {
            $.ajax({
                method: 'PUT',
                url: `/custodian/updateBorrowingStatus/${request_id}`,
                data: {
                    status
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.valid) {
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                        $('#serialsModal').modal('hide');
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON?.msg || 'An error occurred. Please try again.');
                }
            });
        }

        function submitSerialSelection() {
            const request_id = $('#serialsModal').data('request-id');
            const requiredQuantity = $('#serialsModal').data('quantity');
            const selectedSerials = $('input[name="serials[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedSerials.length !== requiredQuantity) {
                showErrorMessage(`Please select exactly ${requiredQuantity} serial(s).`);
                return;
            }

            $.ajax({
                method: 'POST',
                url: `/custodian/releaseWithSerials/${request_id}`,
                data: {
                    serials: selectedSerials
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.valid) {
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                        $('#serialsModal').modal('hide');
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON?.msg || 'An error occurred. Please try again.');
                }
            });
        }

        $(document).ready(function() {
            $('#serialsModal').on('show.bs.modal', function() {
                const request_id = $(this).data('request-id');
                const requiredQuantity = $(this).data('quantity');

                $.get(`/custodian/getAvailableSerials/${request_id}`, function(response) {
                    let html = '';
                    response.serials.forEach(function(serial) {
                        html += `
                        <div class="form-check">
                            <input class="form-check-input serial-checkbox" type="checkbox" name="serials[]" value="${serial.id}">
                            <label class="form-check-label">${serial.serial_number}</label>
                        </div>`;
                    });

                    $('#serialsList').html(html);

                    // Limiting selection logic based on required quantity
                    $('.serial-checkbox').on('change', function() {
                        if ($('.serial-checkbox:checked').length > requiredQuantity) {
                            $(this).prop('checked', false);
                            showErrorMessage(
                                `You can only select up to ${requiredQuantity} serial(s).`
                                );
                        }
                    });
                });
            });
        });
    </script>
@endsection
