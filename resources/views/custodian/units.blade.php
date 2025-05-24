@extends('layouts.base')

@section('PAGE_NAME')
    Units
@endsection

@section('content')
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="toolbar">
                <button class="btn mx-1 me-2 btn-secondary" id="button-add"><i class="fa fa-plus-circle"></i> Add
                    New</button>
            </div>
            <table id="table" data-toggle="table" data-pagination="true" data-search="true" data-url="/units"
                data-toolbar="#toolbar" data-mobile-responsive="true">
                <thead>
                    <tr>
                        <th data-field="count" class="border-2">#</th>
                        <th data-field="name" class="border-2">Unit Name</th>
                        <th data-field="description" class="border-2">Description</th>
                        <th data-field="action" class="border-2">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="addForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Add @yield('PAGE_NAME')</h2>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="name">Unit Name:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="description">Description:</label>
                        <input type="text" name="description" id="description" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="updateForm" class="modal-content">
                <div class="modal-header">
                    <h2 class="h6 modal-title">Update @yield('PAGE_NAME')</h2>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="update_name">Unit Name:</label>
                        <input type="text" name="name" id="update_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="update_description">Description:</label>
                        <input type="text" name="description" id="update_description" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        let unitID;

        function trash(unitId) {
            $.ajax({
                url: `/units/${unitId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.valid) {
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON?.msg || 'An error occurred');
                }
            });
        }

        function update(unitId) {
            $.get(`/units/${unitId}`, function(response) {
                unitID = response.id;
                $('#update_name').val(response.name);
                $('#update_description').val(response.description);
                $('#updateModal').modal('show');
            }).fail(function(jqXHR) {
                showErrorMessage(jqXHR.responseJSON?.msg || 'An error occurred');
            });
        }

        $(document).ready(function() {
            $('#button-add').click(function() {
                $('#addModal').modal('show');
            });

            $('#addForm').submit(function(event) {
                event.preventDefault();
                $.post('/units', $(this).serialize())
                    .done(function(response) {
                        $('#addModal').modal('hide');
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                        $('#addForm')[0].reset();
                    })
                    .fail(function(jqXHR) {
                        showErrorMessage(jqXHR.responseJSON?.msg || 'An error occurred');
                    });
            });

            $('#updateForm').submit(function(event) {
                event.preventDefault();
                $.ajax({
                    url: `/units/${unitID}`,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#updateModal').modal('hide');
                        $('#table').bootstrapTable('refresh');
                        showSuccessMessage(response.msg);
                    },
                    error: function(jqXHR) {
                        showErrorMessage(jqXHR.responseJSON?.msg || 'An error occurred');
                    }
                });
            });
        });
    </script>
@endsection
