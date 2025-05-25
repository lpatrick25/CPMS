@extends('layouts.base')
@section('PAGE_NAME')
    User Account
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
                data-url="/users">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2 rounded-start">#</th>
                        <th data-field="fullname" class="border-2">Fullname</th>
                        <th data-field="email" class="border-2">Email</th>
                        <th data-field="contact_no" class="border-2">Contact</th>
                        <th data-field="role" class="border-2">Role</th>
                        <th data-field="department" class="border-2">Department</th>
                        <th data-field="actions" class="border-2 rounded-end">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="addForm" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h2 class="h6 modal-title">Add @yield('PAGE_NAME')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- First Name -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="first_name">First Name: <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control">
                                <span class="text-danger error-text first_name_error"></span>
                            </div>
                        </div>

                        <!-- Middle Name -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="middle_name">Middle Name:</label>
                                <input type="text" name="middle_name" id="middle_name" class="form-control">
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="last_name">Last Name: <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control">
                                <span class="text-danger error-text last_name_error"></span>
                            </div>
                        </div>

                        <!-- Extension Name -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="extension_name">Extension Name:</label>
                                <input type="text" name="extension_name" id="extension_name" class="form-control">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="email">Email: <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                        </div>

                        <!-- Contact Number -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="contact_no">Contact: <span class="text-danger">*</span></label>
                                <input type="text" name="contact_no" id="contact_no" class="form-control"
                                    data-mask="99999999999">
                                <span class="text-danger error-text contact_no_error"></span>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="password">Password: <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control">
                                <span class="text-danger error-text password_error"></span>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="password_confirmation">Confirm Password: <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control">
                                <span class="text-danger error-text password_confirmation_error"></span>
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="role">User Role: <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control">
                                    <option value="President">President</option>
                                    <option value="Facilities In-charge">Facilities In-charge</option>
                                    <option value="Custodian">Custodian</option>
                                    <option value="System Admin">System Admin</option>
                                    <option value="Employee">Employee</option> <!-- Employee at the end -->
                                </select>
                                <span class="text-danger error-text role_error"></span>
                            </div>
                        </div>

                        <!-- Department (Only enabled for Employee role) -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="department">Department:</label>
                                <input type="text" name="department" id="department" class="form-control" readonly>
                            </div>
                        </div>
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
                    <div class="row">
                        <!-- First Name -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="first_name">First Name: <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control">
                                <span class="text-danger error-text first_name_error"></span>
                            </div>
                        </div>

                        <!-- Middle Name -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="middle_name">Middle Name:</label>
                                <input type="text" name="middle_name" id="middle_name" class="form-control">
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="last_name">Last Name: <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control">
                                <span class="text-danger error-text last_name_error"></span>
                            </div>
                        </div>

                        <!-- Extension Name -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="extension_name">Extension Name:</label>
                                <input type="text" name="extension_name" id="extension_name" class="form-control">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="email">Email: <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                        </div>

                        <!-- Contact Number -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="contact_no">Contact: <span class="text-danger">*</span></label>
                                <input type="text" name="contact_no" id="contact_no" class="form-control"
                                    data-mask="99999999999">
                                <span class="text-danger error-text contact_no_error"></span>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="password">Password: <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control">
                                <span class="text-danger error-text password_error"></span>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="password_confirmation">Confirm Password: <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control">
                                <span class="text-danger error-text password_confirmation_error"></span>
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="role">User Role: <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control">
                                    <option value="President">President</option>
                                    <option value="Facilities In-charge">Facilities In-charge</option>
                                    <option value="Custodian">Custodian</option>
                                    <option value="System Admin">System Admin</option>
                                    <option value="Employee">Employee</option> <!-- Employee at the end -->
                                </select>
                                <span class="text-danger error-text role_error"></span>
                            </div>
                        </div>

                        <!-- Department (Only enabled for Employee role) -->
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="department">Department:</label>
                                <input type="text" name="department" id="department" class="form-control" readonly>
                            </div>
                        </div>
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
        let userID;

        function update(user_id) {
            $.ajax({
                method: 'GET',
                url: `/users/${user_id}`,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response) {
                        userID = response.id; // Use `id` instead of `user_id`

                        $('#updateForm').find('input[id=first_name]').val(response.first_name);
                        $('#updateForm').find('input[id=middle_name]').val(response.middle_name);
                        $('#updateForm').find('input[id=last_name]').val(response.last_name);
                        $('#updateForm').find('input[id=extension_name]').val(response.extension_name);
                        $('#updateForm').find('input[id=email]').val(response.email);
                        $('#updateForm').find('input[id=contact_no]').val(response
                            .contact_no); // Match schema field name
                        $('#updateForm').find('select[id=role]').val(response.role);
                        $('#updateForm').find('input[id=department]').val(response.department);

                        // Enable/Disable Department field based on role
                        if (response.role === 'Employee') {
                            $('#updateForm').find('input[id=department]').prop('readonly', false);
                        } else {
                            $('#updateForm').find('input[id=department]').val('').prop('readonly', true);
                        }

                        $('select').trigger("change");

                        $('#updateModal').modal({
                            backdrop: 'static',
                            keyboard: false,
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

        $(document).ready(function() {
            // Add user modal trigger
            $('#button-add').click(function(event) {
                event.preventDefault();
                $('#addModal').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');
            });

            // Enable/Disable Department based on Role (Add Form)
            $('#addForm').find('select[id=role]').change(function() {
                var departmentField = $('#addForm').find('input[id=department]');
                $(this).val() === 'Employee' ? departmentField.prop('readonly', false) : departmentField
                    .val('').prop('readonly', true);
            });

            // Enable/Disable Department based on Role (Update Form)
            $('#updateForm').find('select[id=role]').change(function() {
                var departmentField = $('#updateForm').find('input[id=department]');
                $(this).val() === 'Employee' ? departmentField.prop('readonly', false) : departmentField
                    .val('').prop('readonly', true);
            });

            // Add User AJAX
            $("#addForm").on("submit", function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "/users",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#addForm').trigger('reset');
                            $('select').trigger("change");
                            showSuccessMessage(response.msg);
                            $('#addModal').modal('hide');
                            $('#table').bootstrapTable('refresh');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $("." + key + "_error").text(value[0]);
                            });
                        }
                    },
                });
            });

            $('#updateForm').submit(function(event) {
                event.preventDefault();

                let formData = new FormData(this);
                formData.append('_method', 'PUT'); // Manually specify PUT method

                $.ajax({
                    url: `/users/${userID}`,
                    method: "POST", // Change to POST since Laravel only supports multipart with POST
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $(".error-text").text("");
                    },
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
                        } else if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
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
