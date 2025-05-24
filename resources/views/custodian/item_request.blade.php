@extends('layouts.base')
@section('PAGE_NAME')
    Stock Requests
@endsection
@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="toolbar">
                <button class="btn mx-1 me-2 btn-secondary" id="button-add"><i class="fa fa-plus-circle"></i>
                    New
            </div>
            <table id="table" data-toggle="table" data-pagination="true" data-search="true" data-show-columns="false"
                data-show-pagination-switch="false" data-show-refresh="false" data-key-events="true"
                data-show-toggle="false" data-resizable="false" data-cookie="true" data-cookie-id-table="saveId"
                data-show-export="false" data-click-to-select="true" data-toolbar="#toolbar" data-defer-url="false"
                data-filter-control="true" data-fixed-columns="true" data-mobile-responsive="true" data-multiple-sort="true"
                data-page-jump-to="true" data-print="true" data-reorder-columns="true" data-sticky-header="true"
                data-url="/itemRequests">
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
    <!-- Custodian View Modal -->
    <div class="modal fade" id="viewItemsCustodianModal" tabindex="-1" aria-labelledby="viewItemsCustodianLabel"
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
                    <button id="custodianApproveBtn" class="btn btn-success">Approve</button>
                    <button id="custodianRejectBtn" class="btn btn-danger">Reject</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="releaseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Release Items</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Date Requested: <span class="text-danger" id="releaseDateRequested"></span></p>
                    <p>Employee Name: <span class="text-danger" id="releaseEmployeeName"></span></p>
                    <p>Transaction Number: <span class="text-danger" id="releaseTransactionNumber"></span></p>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th class="text-center">Requested Quantity</th>
                                <th class="text-center">Unit</th>
                                <th>Approved Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="releaseItemsTable">
                            <!-- Filled dynamically by JS -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" id="releaseConfirmBtn" class="btn btn-success">Confirm Release</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Request Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <form id="addForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">New @yield('PAGE_NAME')</h2>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="employee_id">Employee: <span class="text-danger">*</span></label>
                        <select name="employee_id" id="employee_id" class="form-control" required>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->employee_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Top section with Add Item button aligned to the right -->
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" id="addItemBtn" class="btn btn-secondary">Add Item</button>
                    </div>

                    <!-- Scrollable Table -->
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50%;">Item Name</th>
                                    <th style="width: 25%;">Quantity</th>
                                    <th style="width: 25%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                    <button type="button" class="btn btn-primary text-white-600" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Item Request Modal -->
    <div class="modal fade" id="updateModalQuantity" tabindex="-1" role="dialog"
        aria-labelledby="updateModalQuantity" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="updateFormQuantity" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Update @yield('PAGE_NAME')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="item_id">Item Name: <span class="text-danger">*</span></label>
                        <select name="item_id" id="item_id" class="form-control" required>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="quantity">Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required>
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
                        if (item.status === 'Confirmed') {
                            // Once any item is 'President Approval', Custodian should wait for President
                            showCustodianButtons = false;
                        }

                        if (item.status === 'Approved' || item.status === 'Rejected' || item.status ===
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
                        $('#custodianApproveBtn').show();
                        $('#custodianRejectBtn').show();
                    } else {
                        $('#viewDateReleasedText').show();
                        $('#custodianApproveBtn').hide();
                        $('#custodianRejectBtn').hide();
                    }

                    $('#custodianApproveBtn').data('request-id', requestId);
                    $('#custodianRejectBtn').data('request-id', requestId);

                    $('#viewItemsCustodianModal').modal('show');
                },
                error: function(jqXHR) {
                    alert(jqXHR.responseJSON?.msg || 'An error occurred');
                }
            });
        }

        // Approve Request (Custodian -> President Approval)
        $('#custodianApproveBtn').click(function() {
            const requestId = $(this).data('request-id');
            $.ajax({
                method: 'PUT',
                url: `/custodian/updateStatus/${requestId}`,
                data: {
                    status: 'President Approval'
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#viewItemsCustodianModal').modal('hide');
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

        // Reject Request (Custodian -> Rejected)
        $('#custodianRejectBtn').click(function() {
            const requestId = $(this).data('request-id');
            $.ajax({
                method: 'PUT',
                url: `/custodian/updateStatus/${requestId}`,
                data: {
                    status: 'Rejected'
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#viewItemsCustodianModal').modal('hide');
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

        // Open Release Modal
        function release(requestId) {
            $.ajax({
                type: 'GET',
                url: `/itemRequests/actionViewItems/${requestId}`,
                success: function(response) {
                    $('#releaseDateRequested').text(response.date_requested);
                    $('#releaseEmployeeName').text(response.employee_name);
                    $('#releaseTransactionNumber').text(response.transaction_number);

                    const releaseItemsTable = $('#releaseItemsTable');
                    releaseItemsTable.empty();

                    response.items.forEach(function(item, index) {
                        releaseItemsTable.append(`
                    <tr>
                        <td>${item.item_name}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-center">${item.unit}</td>
                        <td>
                            <input type="number" class="form-control release-quantity-input" name="release_quantity[]" min="1" max="${item.quantity}" value="${item.quantity}" data-item-index="${index}">
                        </td>
                    </tr>
                `);
                    });

                    $('#releaseConfirmBtn').data('request-id', requestId);
                    $('#releaseModal').modal('show');
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

        $('#releaseConfirmBtn').on('click', function() {
            const requestId = $(this).data('request-id');
            const releaseQuantities = [];

            // Collect all release quantities from inputs
            $('.release-quantity-input').each(function() {
                const quantity = $(this).val();
                releaseQuantities.push(quantity);
            });

            $.ajax({
                type: 'PUT',
                url: `/custodian/updateStatus/${requestId}`,
                data: {
                    status: 'Released',
                    release_quantities: releaseQuantities,
                },
                success: function(response) {
                    $('#releaseModal').modal('hide');
                    $('#table').bootstrapTable('refresh');
                    showSuccessMessage(response.msg);
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

        function trash(request_id) {
            $.ajax({
                method: 'DELETE',
                url: `/itemRequests/${request_id}`,
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

        function update(request_id) {
            $.ajax({
                method: 'GET',
                url: `/itemRequests/${request_id}`,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response) {
                        requestID = response.id;
                        $('#updateForm').find('select[id=item_id]').val(response.item_id);
                        $('#updateForm').find('input[id=quantity]').val(response.quantity);
                        $('#updateForm').find('textarea[id=reason]').val(response.reason);
                        $('select').trigger("change");
                        $('#updateModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        }).modal('show');
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

        // Submit Release Request (Custodian -> Released)
        $(document).ready(function() {
            $('#updateForm').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    method: 'PUT',
                    url: `/custodian/updateStatus/${requestID}`,
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


            $('#button-add').click(function() {
                event.preventDefault();
                $('#addModal').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');
            });

            let counter = 1; // Counter for unique row IDs
            let selectedItems = []; // Global array to track selected items

            // Add new item row
            $('#addItemBtn').on('click', function() {
                let availableItems = @json($items); // Fetch items from Laravel
                let filteredItems = availableItems.filter(item => !selectedItems.includes(item.id));

                // Prevent adding if no more items are available
                if (filteredItems.length === 0) {
                    alert("All items are already selected!");
                    return;
                }

                let newRow = `
                    <tr id="item-row-${counter}">
                        <td>
                            <select class="form-control item-select" id="item_id-${counter}" name="item_id[]" required>
                                <option value="" disabled selected>Select Item</option>
                                ${filteredItems.map(item => `<option value="${item.id}">${item.name}</option>`).join('')}
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantity[]" id="quantity-${counter}" class="form-control" min="1" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-block remove-item" data-id="${counter}">
                                Remove
                            </button>
                        </td>
                    </tr>`;

                // Append new row to the table
                $('#itemsTable tbody').append(newRow);
                counter++;

                // Initialize Select2 for the new dropdown
                $("select").select2({
                    width: '100%'
                });

                // Update item selection tracking
                updateItemSelection();
            });

            // Handle item selection change
            $(document).on('change', 'select[name="item_id[]"]', function() {
                let selectedValue = $(this).val();
                let oldValue = $(this).data('old-value');

                // Remove old value from tracking array if it was previously selected
                if (oldValue) {
                    selectedItems = selectedItems.filter(item => item !== oldValue);
                }

                // Add new selection to tracking array
                if (selectedValue) {
                    selectedItems.push(selectedValue);
                }

                // Store the new value in data attribute
                $(this).data('old-value', selectedValue);

                // Refresh dropdowns
                updateItemSelection();
            });

            // Remove item row
            $(document).on('click', '.remove-item', function() {
                let rowId = $(this).data('id'); // Get row ID
                let removedItem = $(`#item_id-${rowId}`).val(); // Get item being removed

                // Remove row
                $(`#item-row-${rowId}`).remove();

                // Remove from tracking array if it was selected
                if (removedItem) {
                    selectedItems = selectedItems.filter(item => item !== removedItem);
                }

                // Refresh dropdowns
                updateItemSelection();
            });

            // Function to update dropdown options dynamically
            function updateItemSelection() {
                $('select[name="item_id[]"]').each(function() {
                    let currentValue = $(this).val(); // Keep the currently selected value

                    $(this).find('option').each(function() {
                        let optionValue = $(this).val();

                        // Hide option if already selected elsewhere (except the current value)
                        if (selectedItems.includes(optionValue) && optionValue !== currentValue) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });

                    // Refresh Select2 for all dropdowns
                    $("select").select2({
                        width: '100%'
                    });
                });
            }

            // Handle form submission
            $('#addForm').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    method: 'POST',
                    url: '/itemRequests',
                    data: $('#addForm').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#addForm').trigger('reset');
                            selectedItems = []; // Reset selected items after submission
                            $('#itemsTable tbody').empty(); // Remove all rows
                            updateItemSelection(); // Refresh dropdowns
                            showSuccessMessage(response.msg);
                            $('#addModal').modal('hide');
                            $('#table').bootstrapTable('refresh');
                        }
                    },
                    error: function(jqXHR) {
                        if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                            showErrorMessage(jqXHR.responseJSON.msg);
                        } else if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                            let errors = jqXHR.responseJSON.errors;
                            let errorMsg = `${jqXHR.responseJSON.msg}\n`;
                            for (const [field, messages] of Object.entries(errors)) {
                                errorMsg += `- ${messages.join(', ')}\n`;
                            }
                            showErrorMessage(errorMsg);
                        } else {
                            showErrorMessage("An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });

            $('#updateFormQuantity').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    method: 'PUT',
                    url: `/itemRequests/${requestID}`,
                    data: $('#updateFormQuantity').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#updateFormQuantity').trigger('reset');
                            showSuccessMessage(response.msg);
                            $('#updateModalQuantity').modal('hide');
                            $('#table').bootstrapTable('refresh');
                        }
                    },
                    error: function(jqXHR) {
                        if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                            let errors = jqXHR.responseJSON.errors;
                            let errorMsg = `${jqXHR.responseJSON.msg}\n`;
                            for (const [field, messages] of Object.entries(errors)) {
                                errorMsg += `- ${messages.join(', ')}\n`;
                            }
                            showErrorMessage(errorMsg);
                        } else {
                            showErrorMessage("An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });
        });
    </script>
@endsection
