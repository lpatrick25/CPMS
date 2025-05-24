@extends('layouts.base')
@section('PAGE_NAME')
    Stocks
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
                data-url="/items">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="name" class="border-2">Item Name</th>
                        <th data-field="description" class="border-2">Item Description</th>
                        <th data-field="unit" class="border-2">Item Unit</th>
                        <th data-field="quantity" class="border-2">Quantity</th>
                        <th data-field="remaining_quantity" class="border-2">Remaining Quantity</th>
                        <th data-field="action" class="border-2 rounded-end">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="addItemForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Add Item</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Item Name -->
                    <div class="form-group mb-4">
                        <label for="name">Item Name: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Item Description -->
                    <div class="form-group mb-4">
                        <label for="description">Item Description: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Item Unit -->
                    <div class="form-group mb-4">
                        <label for="unit">Item Unit: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="unit" id="unit" class="form-control" required>
                        </div>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group mb-4">
                        <label for="quantity">Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required min="0">
                        <small id="quantityError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Remaining Quantity -->
                    <div class="form-group mb-4">
                        <label for="remaining_quantity">Remaining Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="remaining_quantity" id="remaining_quantity" class="form-control"
                            required min="0">
                        <small id="remaining_quantityError" class="form-text text-danger d-none"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                    <button type="button" class="btn btn-primary text-white-600" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="updateForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Update @yield('PAGE_NAME')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Item Name -->
                    <div class="form-group mb-4">
                        <label for="name">Item Name: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Item Description -->
                    <div class="form-group mb-4">
                        <label for="description">Item Description: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Item Unit -->
                    <div class="form-group mb-4">
                        <label for="unit">Item Unit: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="unit" id="unit" class="form-control" required>
                        </div>
                        <small id="nameError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group mb-4">
                        <label for="quantity">Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required
                            min="0">
                        <small id="quantityError" class="form-text text-danger d-none"></small>
                    </div>

                    <!-- Remaining Quantity -->
                    <div class="form-group mb-4">
                        <label for="remaining_quantity">Remaining Quantity: <span class="text-danger">*</span></label>
                        <input type="number" name="remaining_quantity" id="remaining_quantity" class="form-control"
                            required min="0">
                        <small id="remaining_quantityError" class="form-text text-danger d-none"></small>
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
        let itemID;

        // Handle Delete Item
        function trash(item_id) {
            $.ajax({
                method: 'DELETE',
                url: `/items/${item_id}`,
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

        // Handle Update Item
        function update(item_id) {
            $.ajax({
                method: 'GET',
                url: `/items/${item_id}`,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response) {
                        itemID = response.id;
                        $('#updateForm').find('input[id=name]').val(response.name);
                        $('#updateForm').find('input[id=description]').val(response.description);
                        $('#updateForm').find('input[id=unit]').val(response.unit);
                        $('#updateForm').find('input[id=quantity]').val(response.quantity);
                        $('#updateForm').find('input[id=remaining_quantity]').val(response.remaining_quantity);
                        $('#updateModal').modal({
                            backdrop: 'static',
                            keyboard: false,
                        }).modal('show');
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON && jqXHR.responseJSON.msg ? jqXHR.responseJSON.msg :
                        "An unexpected error occurred. Please try again.");
                }
            });
        }

        $(document).ready(function() {

            // Show Add Item Modal
            $('#button-add').click(function(event) {
                event.preventDefault();
                $('#addItemModal').modal({
                    backdrop: 'static',
                    keyboard: false,
                }).modal('show');
            });

            // Add Item Form Submission
            $('#addItemForm').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    method: 'POST',
                    url: '/items',
                    data: $('#addItemForm').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#addItemForm').trigger('reset');
                            $('select').trigger("change");
                            showSuccessMessage(response.msg);
                            $('#addItemModal').modal('hide');
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

            // Update Item Form Submission
            $('#updateForm').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    method: 'PUT',
                    url: `/items/${itemID}`,
                    data: $('#updateForm').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#updateForm').trigger('reset');
                            $('select').trigger("change");
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
