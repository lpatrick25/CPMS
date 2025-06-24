@extends('layouts.base')
@section('PAGE_NAME')
    Stock Requests
@endsection
@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="toolbar">
                <button class="btn mx-1 me-2 btn-secondary" id="button-add"><i class="fa fa-plus-circle"></i>
                    New</button>
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

    <!-- Employee View Modal -->
    <div class="modal fade" id="viewItemsEmployeeModal" tabindex="-1" aria-labelledby="viewItemsEmployeeLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Employee Request Details (View Only)</h5>
                </div>
                <div class="modal-body">
                    <p>Transaction Number: <span class="text-danger" id="viewTransactionNumber"></span></p>
                    <p>Date Requested: <span class="text-danger" id="viewDateRequested"></span></p>
                    <p id="viewDateReleasedText">Release Date: <span class="text-danger" id="viewDateReleased"></span></p>
                    <p>Employee: <span class="text-danger" id="viewEmployeeName"></span></p>
                    <p>Status: <span class="text-danger" id="viewStatus"></span></p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Release Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="viewRequestedItemsTable"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Item Request Modal -->
    <!-- Update Item Request Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="updateForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Request Details</h5>
                </div>
                <div class="modal-body">
                    <p>Transaction Number: <span class="text-danger" id="updateTransactionNumber"></span></p>
                    <p>Date Requested: <span class="text-danger" id="updateDateRequested"></span></p>
                    <p>Employee: <span class="text-danger" id="updateEmployeeName"></span></p>
                    <p>Status: <span class="text-danger" id="updateStatus"></span></p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="updateRequestedItemsTable"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Item Request Modal --><!-- Add Item Request Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <form id="addForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Add @yield('PAGE_NAME')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Top section with Add Item button aligned to the right -->
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" id="addItemBtn" class="btn btn-secondary">Add Item</button>
                    </div>

                    <!-- Scrollable Table -->
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%;">Item Name</th>
                                    <th style="width: 20%;">Unit</th>
                                    <th style="width: 20%;">Quantity</th>
                                    <th style="width: 20%;">Action</th>
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
@endsection
@section('script')
    <script type="text/javascript">
        let requestID;

        function viewItems(requestId) {
            $.ajax({
                type: 'GET',
                url: `/itemRequests/actionViewItems/${requestId}`,
                success: function(response) {
                    $('#viewTransactionNumber').text(response.transaction_number);
                    $('#viewDateRequested').text(response.date_requested);
                    $('#viewDateReleased').text(response.date_released);
                    $('#viewEmployeeName').text(response.employee_name);
                    $('#viewStatus').html(response.status);

                    const itemsTable = $('#viewRequestedItemsTable');
                    itemsTable.empty();

                    let showEmployeeButtons = true;

                    response.items.forEach(function(item) {
                        if (['Confirmed', 'Approved', 'Rejected', 'Released'].includes(item.status)) {
                            showEmployeeButtons = false;
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

                    if (showEmployeeButtons) {
                        $('#viewDateReleasedText').hide();
                    } else {
                        $('#viewDateReleasedText').show();
                    }

                    $('#viewItemsEmployeeModal').modal('show');
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                        showErrorMessage(jqXHR.responseJSON.msg);
                    } else {
                        showErrorMessage("An unexpected error occurred. Please try again.");
                    }
                },
            });
        }

        function update(requestId) {
            $.ajax({
                type: 'GET',
                url: `/itemRequests/actionViewItems/${requestId}`,
                success: function(response) {
                    requestID = requestId;
                    $('#updateTransactionNumber').text(response.transaction_number);
                    $('#updateDateRequested').text(response.date_requested);
                    $('#updateEmployeeName').text(response.employee_name);
                    $('#updateStatus').html(response.status);

                    const itemsTable = $('#updateRequestedItemsTable');
                    itemsTable.empty();

                    response.items.forEach(function(item) {
                        itemsTable.append(`
                            <tr>
                                <td>${item.item_name}</td>
                                <td class="text-center">${item.unit}</td>
                                <td class="text-center">
                                    <input type="number" class="form-control update-quantity" name="quantities[${item.id}]" value="${item.quantity}" min="1">
                                </td>
                            </tr>
                        `);
                    });

                    $('#updateModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                    }).modal('show');
                },
                error: function(jqXHR) {
                    alert(jqXHR.responseJSON?.msg || 'An error occurred');
                },
            });
        }

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

        $(document).ready(function() {
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
                        <td class="text-center unit-cell" id="unit-${counter}">-</td>
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
                let rowId = $(this).attr('id').split('-')[1]; // Extract counter from id (e.g., item_id-1)

                // Remove old value from tracking array if it was previously selected
                if (oldValue) {
                    selectedItems = selectedItems.filter(item => item !== oldValue);
                }

                // Add new selection to tracking array
                if (selectedValue) {
                    selectedItems.push(selectedValue);

                    // Fetch unit for the selected item
                    $.ajax({
                        type: 'GET',
                        url: `/items/${selectedValue}/unit`,
                        success: function(response) {
                            $(`#unit-${rowId}`).text(response.unit_name || '-');
                        },
                        error: function(jqXHR) {
                            $(`#unit-${rowId}`).text('-');
                            showErrorMessage(jqXHR.responseJSON?.msg || 'Failed to fetch unit');
                        }
                    });
                } else {
                    $(`#unit-${rowId}`).text('-');
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
                    let rowId = $(this).attr('id').split('-')[1]; // Extract counter

                    $(this).find('option').each(function() {
                        let optionValue = $(this).val();

                        // Hide option if already selected elsewhere (except the current dropdown's selected value)
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

                    // Re-fetch unit if an item is already selected
                    if (currentValue) {
                        $.ajax({
                            type: 'GET',
                            url: `/items/${currentValue}/unit`,
                            success: function(response) {
                                $(`#unit-${rowId}`).text(response.unit_name || '-');
                            },
                            error: function() {
                                $(`#unit-${rowId}`).text('-');
                            }
                        });
                    }
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
                            showErrorMessage(
                                "An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });

            $('#updateForm').submit(function(event) {
                event.preventDefault();

                const quantities = {};
                $('.update-quantity').each(function() {
                    const itemId = $(this).attr('name').match(/\d+/)[0];
                    quantities[itemId] = $(this).val();
                });

                $.ajax({
                    type: 'PUT',
                    url: `/itemRequests/${requestID}`,
                    data: {
                        quantities: quantities,
                    },
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#updateForm').trigger('reset');
                            showSuccessMessage(response.msg);
                            $('#updateModal').modal('hide');
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
                            showErrorMessage(
                                "An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });
        });
    </script>
@endsection
