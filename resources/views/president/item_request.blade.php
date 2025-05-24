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
    <!-- View Items Modal -->
    <!-- President View Modal -->
    <div class="modal fade" id="viewItemsPresidentModal" tabindex="-1" aria-labelledby="viewItemsCustodianLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Custodian Request Details</h5>
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
                            </tr>
                        </thead>
                        <tbody id="requestedItemsTable"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button id="presidentApproveBtn" class="btn btn-success">Approve</button>
                    <button id="presidentRejectBtn" class="btn btn-danger">Reject</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function viewItems(requestId) {
            $.ajax({
                type: 'GET',
                url: `/itemRequests/actionViewItems/${requestId}`,
                success: function(response) {
                    $('#transactionNumber').text(response.transaction_number);
                    $('#viewDateRequested').text(response.date_requested);
                    $('#viewDateReleased').text(response.date_released);
                    $('#viewEmployeeName').text(response.employee_name);
                    $('#viewStatus').text(response.status);

                    const itemsTable = $('#requestedItemsTable');
                    itemsTable.empty();

                    let showCustodianButtons = true;

                    response.items.forEach(function(item) {
                        if (item.status === 'Approved') {
                            // Once any item is 'President Approval', Custodian should wait for President
                            showCustodianButtons = false;
                        }

                        if (item.status === 'Pending' || item.status === 'Rejected' || item.status ===
                            'Released') {
                            showCustodianButtons = false;
                        }

                        itemsTable.append(`
                            <tr>
                                <td>${item.item_name}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-center">${item.unit}</td>
                                <td class="text-center">${item.release_quantity}</td>
                            </tr>
                        `);
                    });

                    if (showCustodianButtons) {
                        $('#viewDateReleasedText').hide();
                        $('#presidentApproveBtn').show();
                        $('#presidentRejectBtn').show();
                    } else {
                        $('#viewDateReleasedText').show();
                        $('#presidentApproveBtn').hide();
                        $('#presidentRejectBtn').hide();
                    }

                    $('#presidentApproveBtn').data('request-id', requestId);
                    $('#presidentRejectBtn').data('request-id', requestId);

                    $('#viewItemsPresidentModal').modal('show');
                },
                error: function(jqXHR) {
                    alert(jqXHR.responseJSON?.msg || 'An error occurred');
                }
            });
        }

        // President Approve Request
        $('#presidentApproveBtn').click(function() {
            const requestId = $(this).data('request-id');
            $.ajax({
                method: 'PUT',
                url: `/president/updateStatus/${requestId}`,
                data: {
                    status: 'Approved'
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#viewItemsPresidentModal').modal('hide');
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
        });

        // President Reject Request
        $('#presidentRejectBtn').click(function() {
            const requestId = $(this).data('request-id');
            $.ajax({
                method: 'PUT',
                url: `/president/updateStatus/${requestId}`,
                data: {
                    status: 'Rejected'
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#viewItemsPresidentModal').modal('hide');
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
        });

        $(document).ready(function() {});
    </script>
@endsection
