@extends('layouts.base')
@section('PAGE_NAME')
    Tools & Equipment's
@endsection
@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="toolbar">
                <button class="btn mx-1 me-2 btn-secondary" id="button-add"><i class="fa fa-plus-circle"></i> Add New
                    @yield('PAGE_NAME')</button>
            </div>
            <table id="table" data-toggle="table" data-pagination="true" data-search="true" data-show-columns="false"
                data-show-pagination-switch="false" data-show-refresh="false" data-key-events="true"
                data-show-toggle="false" data-resizable="false" data-cookie="true" data-cookie-id-table="saveId"
                data-show-export="false" data-click-to-select="true" data-toolbar="#toolbar" data-defer-url="false"
                data-filter-control="true" data-fixed-columns="true" data-mobile-responsive="true" data-multiple-sort="true"
                data-page-jump-to="true" data-print="true" data-reorder-columns="true" data-sticky-header="true"
                data-url="/equipments">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="name" class="border-2">Item Name</th>
                        <th data-field="description" class="border-2">Item Description</th>
                        <th data-field="category" class="border-2">Item Category</th>
                        <th data-field="quantity" class="border-2">Quantity</th>
                        <th data-field="remaining_quantity" class="border-2">Remaining Quantity</th>
                        <th data-field="has_serial" class="border-2">Has Serial</th>
                        <th data-field="action" class="border-2 rounded-end">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Add Equipment Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="addForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Add Equipment</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Equipment Name -->
                    <div class="form-group mb-4">
                        <label for="name">Equipment Name: <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Equipment Description -->
                    <div class="form-group mb-4">
                        <label for="description">Item Description: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Equipment Type -->
                    <div class="form-group mb-4">
                        <label for="category">Category: <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="Tool">Tool</option>
                            <option value="Equipment">Equipment</option>
                        </select>
                        <small id="typeError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group mb-4">
                        <label for="quantity">Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required min="1">
                        <small id="quantityError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Remaining Quantity -->
                    <div class="form-group mb-4">
                        <label for="remaining_quantity">Remaining Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="remaining_quantity" id="remaining_quantity" class="form-control"
                            required min="0">
                        <small id="remainingQuantityError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Has Serial Number Checkbox -->
                    <div class="form-group mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="has_serial" name="has_serial">
                        <label class="form-check-label" for="has_serial">Has Serial Number</label>
                    </div>

                    <!-- Serial Number Fields (Hidden by Default) -->
                    <div id="serialNumbersSection" class="d-none">
                        <label>Serial Numbers:</label>
                        <div id="serialNumbersContainer">
                            <!-- Serial number fields will be added dynamically here -->
                        </div>
                        <button type="button" id="addSerialNumber" class="btn btn-sm btn-success mt-2">+ Add Serial
                            Number</button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                    <button type="button" class="btn btn-primary text-white-600" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Update Equipment Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="updateForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Update Equipment</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Equipment Name -->
                    <div class="form-group mb-4">
                        <label for="name">Equipment Name: <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Equipment Description -->
                    <div class="form-group mb-4">
                        <label for="description">Item Description: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Equipment Type -->
                    <div class="form-group mb-4">
                        <label for="category">Category: <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="Tool">Tool</option>
                            <option value="Equipment">Equipment</option>
                        </select>
                        <small id="typeError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group mb-4">
                        <label for="quantity">Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required
                            min="1">
                        <small id="quantityError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Remaining Quantity -->
                    <div class="form-group mb-4">
                        <label for="remaining_quantity">Remaining Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="remaining_quantity" id="remaining_quantity" class="form-control"
                            required min="0">
                        <small id="remainingQuantityError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Has Serial Number Checkbox -->
                    <div class="form-group mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="has_serial" name="has_serial">
                        <label class="form-check-label" for="has_serial">Has Serial Number</label>
                    </div>

                    <!-- Serial Number Fields (Hidden by Default) -->
                    <div id="serialNumbersSection" class="d-none">
                        <label>Serial Numbers:</label>
                        <div id="serialNumbersContainer">
                            <!-- Serial number fields will be added dynamically here -->
                        </div>
                        <button type="button" id="addSerialNumber" class="btn btn-sm btn-success mt-2">+ Add Serial
                            Number</button>
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
        let equipmentID;

        // OPEN UPDATE MODAL + POPULATE FORM
        function update(equipment_id) {
            $('#updateForm').trigger('reset');
            $('#updateForm').find("#serialNumbersContainer").html('');
            $('#updateForm').find('#has_serial').prop('checked', false);

            $.ajax({
                method: 'GET',
                url: `/equipments/${equipment_id}`,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response) {
                        equipmentID = response.id;
                        const form = $('#updateForm');

                        form.find('#name').val(response.name);
                        form.find('#description').val(response.description);
                        form.find('#category').val(response.category);
                        form.find('#quantity').val(response.quantity);
                        form.find('#remaining_quantity').val(response.remaining_quantity);
                        form.find('#has_serial').prop('checked', response.has_serial);
                        form.find('select').trigger("change");

                        // Show serials if equipment has them
                        const serialNumbersContainer = form.find("#serialNumbersContainer");
                        if (response.has_serial) {
                            response.serials.forEach(serial => {
                                let serialInput = createSerialInput(serial.serial_number);
                                serialNumbersContainer.append(serialInput);
                            });
                        }

                        // Call this after setting category and serial state
                        toggleFieldsBasedOnCategory('#updateForm');

                        $('#updateModal').modal('show');
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON?.msg || "An error occurred. Please try again.");
                }
            });
        }

        // Handle Delete Item
        function trash(equipment_id) {
            $.ajax({
                method: 'DELETE',
                url: `/equipments/${equipment_id}`,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON && jqXHR.responseJSON.msg ? jqXHR.responseJSON.msg :
                        "An unexpected error occurred. Please try again.");
                }
            });
        }

        $(document).ready(function() {
            // OPEN ADD MODAL
            $('#button-add').click(function(event) {
                event.preventDefault();
                const form = $('#addForm');
                form.trigger('reset');
                form.find("#serialNumbersContainer").html('');
                toggleFieldsBasedOnCategory('#addForm');
                $('#addModal').modal('show');
            });

            // CATEGORY CHANGE LOGIC
            $('#addForm #category, #updateForm #category').change(function() {
                const formSelector = $(this).closest('form');
                toggleFieldsBasedOnCategory(`#${formSelector.attr('id')}`);
            });

            // HAS_SERIAL CHECKBOX CHANGE LOGIC
            $('#addForm #has_serial, #updateForm #has_serial').change(function() {
                const formSelector = $(this).closest('form');
                toggleSerialFields(`#${formSelector.attr('id')}`);
            });

            // ADD SERIAL NUMBER BUTTON
            $('#addForm #addSerialNumber, #updateForm #addSerialNumber').click(function() {
                const formSelector = $(this).closest('form');
                addSerialNumber(`#${formSelector.attr('id')}`);
            });

            // REMOVE SERIAL NUMBER INPUT FIELD
            $(document).on('click', '.removeSerial', function() {
                $(this).parent().remove();
            });

            // ADD FORM SUBMISSION
            $('#addForm').submit(function(event) {
                event.preventDefault();
                handleFormSubmission('#addForm', 'POST', '/equipments');
            });

            // UPDATE FORM SUBMISSION
            $('#updateForm').submit(function(event) {
                event.preventDefault();
                handleFormSubmission('#updateForm', 'POST', `/equipments/${equipmentID}?_method=PUT`);
            });
        });

        // DYNAMICALLY CONTROL FORM FIELDS BASED ON CATEGORY AND SERIAL CHECKBOX
        function toggleFieldsBasedOnCategory(formSelector) {
            const form = $(formSelector);
            const category = form.find('#category').val();
            const hasSerialCheckbox = form.find('#has_serial');
            const quantityField = form.find("#quantity");
            const remainingQuantityField = form.find("#remaining_quantity");
            const serialNumbersSection = form.find("#serialNumbersSection");
            const serialNumbersContainer = form.find("#serialNumbersContainer");

            if (category === 'Tool') {
                hasSerialCheckbox.prop('checked', false).prop('disabled', true);
                quantityField.prop('disabled', false);
                remainingQuantityField.prop('disabled', false);
                serialNumbersSection.addClass('d-none');
                serialNumbersContainer.html('');
            } else if (category === 'Equipment') {
                hasSerialCheckbox.prop('disabled', false);

                if (hasSerialCheckbox.is(':checked')) {
                    quantityField.val('').prop('disabled', true);
                    remainingQuantityField.val('').prop('disabled', true);
                    serialNumbersSection.removeClass('d-none');

                    if (serialNumbersContainer.children().length === 0) {
                        addSerialNumber(formSelector);
                    }
                } else {
                    quantityField.prop('disabled', false);
                    remainingQuantityField.prop('disabled', false);
                    serialNumbersSection.addClass('d-none');
                    serialNumbersContainer.html('');
                }
            }
        }

        function toggleSerialFields(formSelector) {
            const form = $(formSelector);
            const hasSerial = form.find('#has_serial').is(':checked');
            const serialNumbersSection = form.find("#serialNumbersSection");
            const quantityField = form.find("#quantity");
            const remainingQuantityField = form.find("#remaining_quantity");

            if (hasSerial) {
                quantityField.val('').prop('disabled', true);
                remainingQuantityField.val('').prop('disabled', true);
                serialNumbersSection.removeClass('d-none');

                if (form.find("#serialNumbersContainer").children().length === 0) {
                    addSerialNumber(formSelector);
                }
            } else {
                quantityField.prop('disabled', false);
                remainingQuantityField.prop('disabled', false);
                serialNumbersSection.addClass('d-none');
                form.find("#serialNumbersContainer").html('');
            }
        }

        // CREATE SERIAL NUMBER INPUT FIELD
        function createSerialInput(serialNumber = '') {
            return $(`
            <div class="input-group mb-2">
                <input type="text" name="serial_numbers[]" class="form-control" required value="${serialNumber}" placeholder="Enter Serial Number">
                <button type="button" class="btn btn-danger removeSerial">X</button>
            </div>
        `);
        }

        // ADD A SERIAL NUMBER FIELD
        function addSerialNumber(formSelector) {
            const newSerial = createSerialInput();
            $(formSelector).find("#serialNumbersContainer").append(newSerial);
        }

        // HANDLE FORM SUBMISSION (ADD / UPDATE)
        function handleFormSubmission(formSelector, method, url) {
            const formElement = $(formSelector)[0];
            const formData = new FormData(formElement);

            // Handle the checkbox manually (convert 'on' to true/false)
            const hasSerialCheckbox = $(formSelector).find('#has_serial');
            formData.set('has_serial', hasSerialCheckbox.is(':checked') ? '1' : '0');

            $.ajax({
                method: method,
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $(formSelector)[0].reset();
                        $(formSelector).closest('.modal').modal('hide');
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                    }
                },
                error: function(jqXHR) {
                    let errorMsg = jqXHR.responseJSON?.msg || "An error occurred. Please try again.";
                    showErrorMessage(errorMsg);
                }
            });
        }
    </script>
@endsection
