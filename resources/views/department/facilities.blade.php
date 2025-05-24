@extends('layouts.base')
@section('PAGE_NAME')
    Facilities
@endsection
@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="toolbar">
                <button class="btn mx-1 me-2 btn-secondary" id="button-add"><i class="fa fa-plus-circle"></i> Add
                    @yield('PAGE_NAME')</button>
            </div>
            <table id="table" data-toggle="table" data-pagination="true" data-search="true" data-show-columns="false"
                data-show-pagination-switch="false" data-show-refresh="false" data-key-events="true"
                data-show-toggle="false" data-resizable="false" data-cookie="true" data-cookie-id-table="saveId"
                data-show-export="false" data-click-to-select="true" data-toolbar="#toolbar" data-defer-url="false"
                data-filter-control="true" data-fixed-columns="true" data-mobile-responsive="true" data-multiple-sort="true"
                data-page-jump-to="true" data-print="true" data-reorder-columns="true" data-sticky-header="true"
                data-url="/facilities">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="facility_name" class="border-2">Facility Name</th>
                        <th data-field="facility_description" class="border-2">Facility Description</th>
                        <th data-field="facility_status" class="border-2">Facility Status</th>
                        <th data-field="facility_in_charge" class="border-2 rounded-end">In-charge</th>
                        <th data-field="action" class="border-2 rounded-end">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="addForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Add @yield('PAGE_NAME')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="facility_name">Facility Name: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="facility_name" id="facility_name" class="form-control">
                            <input type="hidden" name="facility_in_charge" id="facility_in_charge" class="form-control" value="{{ auth()->user()->id }}">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="facility_description">Facility Description: <span class="text-danger"></span></label>
                        <div class="input-group">
                            <input type="text" name="facility_description" id="facility_description" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="facility_status">Facility Status: <span class="text-danger">*</span></label>
                        <select name="facility_status" id="facility_status" class="form-control">
                            <option value="Available">Available</option>
                            <option value="Under Maintenance">Under Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                    <button type="button" class="btn btn-primary text-white-600" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="updateForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Update @yield('PAGE_NAME')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="facility_name">Facility Name: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="facility_name" id="facility_name" class="form-control">
                            <input type="hidden" name="facility_in_charge" id="facility_in_charge" class="form-control" value="{{ auth()->user()->id }}">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="facility_description">Facility Description: <span class="text-danger"></span></label>
                        <div class="input-group">
                            <input type="text" name="facility_description" id="facility_description" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="facility_status">Facility Status: <span class="text-danger">*</span></label>
                        <select name="facility_status" id="facility_status" class="form-control">
                            <option value="Available">Available</option>
                            <option value="Under Maintenance">Under Maintenance</option>
                        </select>
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
        let facilityID;

        function trash(facility_id) {
            $.ajax({
                method: 'DELETE',
                url: `/facilities/${facility_id}`,
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
                        showErrorMessage(jqXHR.responseJSON
                            .msg); // Show general error message
                    } else {
                        showErrorMessage(
                            "An unexpected error occurred. Please try again."); // Generic error message
                    }
                }

            });
        }

        function update(facility_id) {
            $.ajax({
                method: 'GET',
                url: `/facilities/${facility_id}`,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response) {
                        facilityID = response.id;
                        $('#updateForm').find('input[id=facility_name]').val(response.facility_name);
                        $('#updateForm').find('input[id=facility_description]').val(response.facility_description);
                        $('#updateForm').find('select[id=facility_status]').val(response.facility_status);
                        $('select').trigger(
                            "change"
                        );
                        $('#updateModal').modal({
                            backdrop: 'static',
                            keyboard: false,
                        }).modal('show');
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                        showErrorMessage(jqXHR.responseJSON
                            .msg); // Show general error message
                    } else {
                        showErrorMessage(
                            "An unexpected error occurred. Please try again."); // Generic error message
                    }
                }

            });
        }

        $(document).ready(function() {

            $('#button-add').click(function() {
                event.preventDefault();

                $('#addModal').modal({
                    backdrop: 'static',
                    keyboard: false,
                }).modal('show');
            });

            $('#addForm').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                $.ajax({
                    method: 'POST',
                    url: '/facilities',
                    data: $('#addForm').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#addForm').trigger('reset'); // Reset the form
                            $('select').trigger(
                                "change"
                            ); // Reset select elements (if using select2 or similar)
                            showSuccessMessage(response.msg); // Show success message
                            $('#addModal').modal('hide');
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
                            showErrorMessage(errorMsg); // Show validation errors
                        } else if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                            showErrorMessage(jqXHR.responseJSON
                                .msg); // Show general error message
                        } else {
                            showErrorMessage(
                                "An unexpected error occurred. Please try again."
                            ); // Generic error message
                        }
                    }
                });
            });

            $('#updateForm').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                $.ajax({
                    method: 'PUT',
                    url: `/facilities/${facilityID}`,
                    data: $('#updateForm').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#updateForm').trigger('reset'); // Reset the form
                            $('select').trigger(
                                "change"
                            ); // Reset select elements (if using select2 or similar)
                            showSuccessMessage(response.msg); // Show success message
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
                            showErrorMessage(errorMsg); // Show validation errors
                        } else if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                            showErrorMessage(jqXHR.responseJSON
                                .msg); // Show general error message
                        } else {
                            showErrorMessage(
                                "An unexpected error occurred. Please try again."
                            ); // Generic error message
                        }
                    }
                });
            });

        });
    </script>
@endsection
