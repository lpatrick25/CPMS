@extends('layouts.base')
@section('PAGE_NAME')
    Item Requests
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
                data-print="true" data-reorder-columns="true" data-sticky-header="true" data-url="/itemRequests">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="transaction_number" class="border-2">Transaction No</th>
                        <th data-field="employee_name" class="border-2">Employee</th>
                        <th data-field="date_requested" class="border-2">Date Requested</th>
                        <th data-field="status" class="border-2">Status</th>
                        <th data-field="action" class="border-2 rounded-end">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- President View Modal -->
    <div class="modal fade" id="viewItemsPresidentModal" tabindex="-1" aria-labelledby="viewItemsPresidentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">President Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Transaction Number: <span class="text-danger" id="transactionNumber"></span></p>
                    <p>Date Requested: <span class="text-danger" id="viewDateRequested"></span></p>
                    <p id="viewDateReleasedText">Release Date: <span class="text-danger" id="viewDateReleased"></span></p>
                    <p>Employee: <span class="text-danger" id="viewEmployeeName"></span></p>
                    <p>Status: <span class="text-danger" id="viewStatus"></span></p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Approved Quantity</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="requestedItemsTable"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        let requestIds;
        function viewItems(request_id) {
            $.ajax({
                type: 'GET',
                url: `/itemRequests/actionViewItems/${request_id}`,
                success: function(response) {
                    $('#transactionNumber').text(response.transaction_number);
                    $('#viewDateRequested').text(response.date_requested);
                    $('#viewDateReleased').text(response.date_released);
                    $('#viewEmployeeName').text(response.employee_name);
                    $('#viewStatus').text(response.status);

                    requestIds = request_id; // Store the request ID for later use

                    const itemsTable = $('#requestedItemsTable');
                    itemsTable.empty();

                    let showActionButtons = false;

                    // Filter items to show only those in 'President Approval' status
                    response.items.forEach(function(item) {
                        let actionButtons = '';

                        if (item.status === 'Confirmed') { // 'Confirmed' maps to 'President Approval'
                            showActionButtons = true;
                            actionButtons = `
                                <td class="text-center">
                                    <button class="btn btn-success btn-sm item-approve-btn" data-item-id="${item.item_request_id}" data-request-id="${item.item_request_id}">Approve</button>
                                    <button class="btn btn-danger btn-sm item-reject-btn" data-item-id="${item.item_request_id}" data-request-id="${item.item_request_id}">Reject</button>
                                </td>
                            `;
                        } else {
                            actionButtons = '<td class="text-center">-</td>';
                        }

                        // Only append items in 'President Approval' status
                        if (item.status === 'Confirmed') {
                            itemsTable.append(`
                                <tr>
                                    <td>${item.item_name}</td>
                                    <td class="text-center">${item.quantity}</td>
                                    <td class="text-center">${item.unit}</td>
                                    <td class="text-center">${item.release_quantity}</td>
                                    ${actionButtons}
                                </tr>
                            `);
                        }
                    });

                    // Show or hide release date based on whether actions are available
                    $('#viewDateReleasedText').toggle(!showActionButtons);

                    $('#viewItemsPresidentModal').modal('show');
                },
                error: function(jqXHR) {
                    alert(jqXHR.responseJSON?.msg || 'An error occurred');
                }
            });
        }

        // Individual Item Approve (President -> Approved)
        $(document).on('click', '.item-approve-btn', function() {
            const itemId = $(this).data('item-id');
            const requestId = $(this).data('request-id');
            $.ajax({
                method: 'PUT',
                url: `/itemRequests/updateItemStatus/${requestId}`,
                data: {
                    status: 'Approved'
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        viewItems(requestIds); // Refresh modal
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON?.msg || 'An error occurred.');
                }
            });
        });

        // Individual Item Reject (President -> Rejected)
        $(document).on('click', '.item-reject-btn', function() {
            const itemId = $(this).data('item-id');
            const requestId = $(this).data('request-id');
            $.ajax({
                method: 'PUT',
                url: `/itemRequests/updateItemStatus/${requestId}`,
                data: {
                    status: 'Rejected'
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        viewItems(requestIds); // Refresh modal
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON?.msg || 'An error occurred.');
                }
            });
        });

        $(document).ready(function() {});
    </script>
@endsection
