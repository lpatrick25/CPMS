@extends('layouts.base')
@section('PAGE_NAME')
    Equipment Reservations
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
                data-url="/equipmentRequests">
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

    <!-- Add Equipment Request Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <form id="addForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Add @yield('PAGE_NAME')</h2>
                </div>
                <div class="modal-body">
                    <!-- Table for Equipments -->
                    <button type="button" id="addEquipmentBtn" class="btn btn-primary">Add Equipment</button>
                    <table class="table table-bordered" id="equipmentsTable">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Equipment Name</th>
                                <th style="width: 25%;">Quantity</th>
                                <th style="width: 25%;">Date of Usage</th>
                                <th style="width: 25%;">Date of Return</th>
                                <th style="width: 25%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic rows will be added here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                    <button type="button" class="btn btn-primary text-white-600" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
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
                        <label for="equipment_id">Equipment Name: <span class="text-danger">*</span></label>
                        <select name="equipment_id" id="equipment_id" class="form-control" required>
                            @foreach ($equipments as $equipment)
                                <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="quantity">Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="date_of_usage">Date of Usage: <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_usage" id="date_of_usage" class="form-control"
                            min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="date_of_return">Date of Return: <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_return" id="date_of_return" class="form-control"
                            min="{{ date('Y-m-d') }}" required>
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
        let borrowingID;

        function trash(borrowing_id) {
            $.ajax({
                method: 'DELETE',
                url: `/equipmentRequests/${borrowing_id}`,
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

        function update(borrowing_id) {
            $.ajax({
                method: 'GET',
                url: `/equipmentRequests/${borrowing_id}`, // Adjust URL if necessary
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response) {
                        // Assuming 'response' contains the data from your controller
                        borrowingID = response.id;
                        $('#updateForm').find('select[id=equipment_id]').val(response.equipment_id);
                        $('#updateForm').find('input[id=quantity]').val(response.quantity);
                        $('#updateForm').find('input[id=date_of_usage]').val(response.date_of_usage);
                        $('#updateForm').find('input[id=date_of_return]').val(response.date_of_return);

                        // Trigger the change event for select dropdown if needed
                        $('select').trigger("change");

                        // Show the modal
                        $('#updateModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        }).modal('show');
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                        showErrorMessage(jqXHR.responseJSON.msg); // Show error from response
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
            let selectedEquipments = []; // Global array to track selected equipments

            // Add new equipment row
            $('#addEquipmentBtn').on('click', function() {
                let availableEquipments = @json($equipments); // Fetch equipments from Laravel
                let filteredEquipments = availableEquipments.filter(equipment => !selectedEquipments
                    .includes(equipment.id));

                // Prevent adding if no more equipments are available
                if (filteredEquipments.length === 0) {
                    alert("All equipments are already selected!");
                    return;
                }

                let today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format

                let newRow = `
                    <tr id="equipment-row-${counter}">
                        <td>
                            <select class="form-control equipment-select" name="equipment_id[]" required>
                                <option value="" disabled selected>Select Equipment</option>
                                ${filteredEquipments.length > 0
                                    ? filteredEquipments.map(equipment => `<option value="${equipment.id}">${equipment.name}</option>`).join('')
                                    : '<option value="" disabled>No Equipment Available</option>'
                                }
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantity[]" class="form-control equipment-quantity" min="1" required>
                        </td>
                        <td>
                            <input type="date" name="date_of_usage[]" class="form-control date-of-usage" min="${today}" required>
                        </td>
                        <td>
                            <input type="date" name="date_of_return[]" class="form-control date-of-return" min="${today}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-block remove-equipment" data-id="${counter}">
                                Remove
                            </button>
                        </td>
                    </tr>
                `;


                // Append new row to the table
                $('#equipmentsTable tbody').append(newRow);
                counter++;

                // Initialize Select2 for the new dropdown
                $("select").select2({
                    width: '100%'
                });

                // Update equipment selection tracking
                updateEquipmentSelection();
            });

            // Handle equipment selection change
            $(document).on('change', 'select[name="equipment_id[]"]', function() {
                let selectedValue = $(this).val();
                let oldValue = $(this).data('old-value');

                // Remove old value from tracking array if it was previously selected
                if (oldValue) {
                    selectedEquipments = selectedEquipments.filter(equipment => equipment !== oldValue);
                }

                // Add new selection to tracking array
                if (selectedValue) {
                    selectedEquipments.push(selectedValue);
                }

                // Store the new value in data attribute
                $(this).data('old-value', selectedValue);

                // Refresh dropdowns
                updateEquipmentSelection();
            });

            // Remove equipment row
            $(document).on('click', '.remove-equipment', function() {
                let rowId = $(this).data('id'); // Get row ID
                let removedEquipment = $(`#equipment_id-${rowId}`).val(); // Get equipment being removed

                // Remove row
                $(`#equipment-row-${rowId}`).remove();

                // Remove from tracking array if it was selected
                if (removedEquipment) {
                    selectedEquipments = selectedEquipments.filter(equipment => equipment !==
                        removedEquipment);
                }

                // Refresh dropdowns
                updateEquipmentSelection();
            });

            // Function to update dropdown options dynamically
            function updateEquipmentSelection() {
                $('select[name="equipment_id[]"]').each(function() {
                    let currentValue = $(this).val(); // Keep the currently selected value

                    $(this).find('option').each(function() {
                        let optionValue = $(this).val();

                        // Hide option if already selected elsewhere (except the current value)
                        if (selectedEquipments.includes(optionValue) && optionValue !==
                            currentValue) {
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

            // Handle Date of Usage Change for the Add Form
            $('#addForm').on('change', '#date_of_usage', function() {
                var usageDate = new Date($(this).val()); // Get the date of usage
                usageDate.setDate(usageDate.getDate() + 1); // Add 1 day

                // Format the new date as YYYY-MM-DD
                var returnDate = usageDate.toISOString().split('T')[0];

                // Set the Date of Return field to the new date
                $('#date_of_return').val(returnDate);
            });

            // Handle Date of Usage Change for the Update Form
            $('#updateForm').on('change', '#date_of_usage', function() {
                var usageDate = new Date($(this).val()); // Get the date of usage
                usageDate.setDate(usageDate.getDate() + 1); // Add 1 day

                // Format the new date as YYYY-MM-DD
                var returnDate = usageDate.toISOString().split('T')[0];

                // Set the Date of Return field to the new date
                $('#date_of_return').val(returnDate);
            });

            // Handle form submission
            $('#addForm').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    method: 'POST',
                    url: '/equipmentRequests',
                    data: $('#addForm').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#addForm').trigger('reset');
                            selectedEquipments
                                = []; // Reset selected equipments after submission
                            $('#equipmentsTable tbody').empty(); // Remove all rows
                            updateEquipmentSelection(); // Refresh dropdowns
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

            $('#updateForm').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    method: 'PUT',
                    url: `/equipmentRequests/${borrowingID}`,
                    data: $('#updateForm').serialize(),
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
                            showErrorMessage("An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });
        });
    </script>
@endsection
