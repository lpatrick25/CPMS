@extends('layouts.base')
@section('PAGE_NAME')
    User Management
@endsection
@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="toolbar">
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
                        <th data-field="actions" class="border-2 rounded-end">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="updateForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Update Password</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="password">Password: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control">
                            <span class="input-group-text" id="basic-addon2">
                                <i class="fa fa-check"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="password_confirmation">Confirm Password: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control">
                            <span class="input-group-text" id="basic-addon2">
                                <i class="fa fa-check"></i>
                            </span>
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
                        userID = response.user_id;
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

            $('#updateForm').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                $.ajax({
                    method: 'PUT',
                    url: `/updatePassword/${userID}`,
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
