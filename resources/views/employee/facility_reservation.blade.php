@extends('layouts.base')
@section('PAGE_NAME')
    Facilities Reservation
@endsection
@section('content')
    <input type="hidden" class="form-control" id="facility_id" value="{{ $facility_id }}">
    <input type="hidden" class="form-control" id="user_id" value="{{ auth()->user()->id }}">
    <!-- Facility Reservation Guide Button -->
    <button type="button" class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#reservationGuideModal">
        View Reservation Guide
    </button>

    <!-- Facility Reservation Guide Modal -->
    <div class="modal fade" id="reservationGuideModal" tabindex="-1" aria-labelledby="reservationGuideModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="reservationGuideModalLabel">Facility Reservation Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>📅 Step 1: Select a Reservation Date</h6>
                    <p>Click on your preferred date on the calendar. You can only select a date that is <strong>at least 7
                            days from today</strong>. A reservation form will appear when you click a valid date.</p>

                    <h6>📝 Step 2: Fill in the Reservation Form</h6>
                    <ul>
                        <li><strong>Facility Name:</strong> Automatically filled based on the selected facility.</li>
                        <li><strong>Reservation Date:</strong> Automatically filled with the selected date.</li>
                        <li><strong>Start Time:</strong> Choose your reservation start time.</li>
                        <li><strong>End Time:</strong> Choose your reservation end time.</li>
                    </ul>

                    <h6>✅ Step 3: Confirm Your Reservation</h6>
                    <p>Click <strong>Confirm Reservation</strong> to submit your request. Click <strong>Cancel</strong> to
                        close the form without saving.</p>

                    <h6>⏳ Step 4: After Submission</h6>
                    <p>Once submitted successfully, you will see a success message. Your reservation will be displayed on
                        the calendar:</p>
                    <ul>
                        <li><span class="badge bg-warning">Pending (Yellow)</span>: Reservation is awaiting approval.</li>
                        <li><span class="badge bg-primary">Confirmed (Blue)</span>: Reservation has been approved and is
                            locked.</li>
                    </ul>

                    <h6>🛑 Important Reminders:</h6>
                    <ul>
                        <li>Reservations must be made <strong>at least 1 day in advance</strong>.</li>
                        <li><strong>Confirmed reservations</strong> cannot be edited.</li>
                        <li><strong>Pending reservations</strong> can be edited or moved by dragging them on the calendar.
                        </li>
                    </ul>

                    <h6>❓ Need Help?</h6>
                    <p>If you experience any issues, please contact the support team.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-5 shadow mb-4">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="addForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">@yield('PAGE_NAME')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="facility_name">Facility Name: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="facility_name" id="facility_name" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="reservation_date">Reservation Date: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="reservation_date" id="reservation_date" class="form-control"
                                readonly>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="start_time">Start Time: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="time" name="start_time" id="start_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="end_time">End Time: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="time" name="end_time" id="end_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="end_time">Purpose: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <textarea name="purpose" id="purpose" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary" id="confirmReservation">Confirm Reservation</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="updateForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">@yield('PAGE_NAME')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="facility_name">Facility Name: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="facility_name" id="facility_name" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="reservation_date">Reservation Date: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="date" name="reservation_date" id="reservation_date" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="start_time">Start Time: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="time" name="start_time" id="start_time" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="end_time">End Time: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="time" name="end_time" id="end_time" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="end_time">Purpose: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <textarea name="purpose" id="purpose" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary" id="confirmReservation">Save
                        Changes</button>
                    <button type="button" class="btn btn-danger" id="deleteReservation">Delete</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            var calendarEl = document.getElementById('calendar');
            var addModal = new bootstrap.Modal(document.getElementById('addModal'));
            var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));

            var selectedReservation = null;
            var reservationID = null;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                themeSystem: 'bootstrap',
                events: function(info, successCallback, failureCallback) {
                    $.ajax({
                        method: 'GET',
                        url: `/facilityReservations/${$('#facility_id').val()}`,
                        data: {
                            'user_id': $('#user_id').val(),
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.valid) {
                                const events = response.reservations.map(function(
                                    reservation) {
                                    return {
                                        title: reservation.status_label ||
                                            'No Title', // Ensure title isn't undefined
                                        start: reservation.reservation_date + 'T' +
                                            reservation.start_time,
                                        end: reservation.reservation_date + 'T' +
                                            reservation.end_time,
                                        extendedProps: {
                                            facility_name: reservation
                                                .facility_name,
                                            reservation_date: reservation
                                                .reservation_date,
                                            start_time: reservation.start_time,
                                            end_time: reservation.end_time,
                                            purpose: reservation.purpose,
                                            reservation_id: reservation.id,
                                            status: reservation.status,
                                            isEditable: reservation.is_editable
                                        },
                                        backgroundColor: reservation.status ===
                                            'Confirmed' ? '#0073b7' : '#f39c12',
                                        borderColor: reservation.status ===
                                            'Confirmed' ? '#0073b7' : '#f39c12',
                                    };
                                });
                                successCallback(events);
                            } else {
                                failureCallback("Failed to load events");
                            }
                        },
                        error: function() {
                            failureCallback("Failed to load events");
                        }
                    });
                },
                editable: true,
                droppable: false,
                dateClick: function(info) {
                    var selectedDate = moment(info.dateStr).startOf('day');
                    var currentDate = moment().startOf('day');

                    if (selectedDate.isBefore(currentDate)) {
                        showErrorMessage("Reservations cannot be made for past dates.");
                        return;
                    }

                    $.ajax({
                        method: 'GET',
                        url: `/facilities/${$('#facility_id').val()}`,
                        dataType: 'JSON',
                        success: function(response) {
                            if (response) {
                                $('#addForm input[name="facility_name"]').val(response
                                    .facility_name);
                                $('#addForm input[name="reservation_date"]').val(
                                    selectedDate.format('YYYY-MM-DD'));

                                // Auto-set min time for today's date
                                if (selectedDate.isSame(currentDate)) {
                                    const now = moment();
                                    const minTime = now.format('HH:mm');
                                    $('#start_time').attr('min', minTime);
                                } else {
                                    $('#start_time').removeAttr('min');
                                }

                                $('#start_time').val('');
                                $('#end_time').val('');
                                $('#purpose').val('');
                                addModal.show();
                            }
                        },
                        error: function() {
                            showErrorMessage("Failed to fetch facility details.");
                        }
                    });
                },
                eventClick: function(info) {
                    if (info.event.extendedProps.isEditable) {
                        selectedReservation = info.event.extendedProps;
                        reservationID = selectedReservation.reservation_id;

                        $('#updateForm input[name="facility_name"]').val(selectedReservation
                            .facility_name);
                        $('#updateForm input[name="reservation_date"]').val(selectedReservation
                            .reservation_date);
                        $('#updateForm input[name="start_time"]').val(selectedReservation.start_time);
                        $('#updateForm input[name="end_time"]').val(selectedReservation.end_time);
                        $('#updateForm textarea[name="purpose"]').val(selectedReservation.purpose);

                        updateModal.show();
                    } else {
                        showSuccessMessage("This reservation is confirmed and cannot be edited.");
                    }
                },
                eventDrop: function(info) {
                    const selectedDate = moment(info.event.start).format('YYYY-MM-DD');
                    const currentDate = moment().startOf('day');
                    const minAllowedDate = currentDate.clone().add(0, 'days');

                    if (moment(selectedDate).isBefore(minAllowedDate, 'day')) {
                        showErrorMessage("Reservations cannot be rescheduled to past dates.");
                        info.revert();
                        return;
                    }

                    const startTime = moment(info.event.start).format('HH:mm:ss');
                    const endTime = info.event.end ? moment(info.event.end).format('HH:mm:ss') : null;

                    const updatedReservationData = {
                        reservation_date: selectedDate,
                        start_time: startTime,
                        end_time: endTime,
                        status: info.event.extendedProps.status,
                        purpose: info.event.extendedProps.purpose,
                    };

                    $.ajax({
                        method: 'PUT',
                        url: `/facilityReservations/${info.event.extendedProps.reservation_id}`,
                        data: updatedReservationData,
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.valid) {
                                showSuccessMessage('Reservation successfully updated.');
                            } else {
                                showErrorMessage(response.msg ||
                                    'Failed to update reservation.');
                                info.revert();
                            }
                        },
                        error: function(jqXHR) {
                            let errorMsg =
                            "An unexpected error occurred. Please try again.";
                            if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                                errorMsg = jqXHR.responseJSON.msg;
                            }
                            showErrorMessage(errorMsg);
                            info.revert();
                        }
                    });
                }
            });

            calendar.render();

            const startTimeInput = $('#start_time');
            const endTimeInput = $('#end_time');
            const reservationDateInput = $('#reservation_date');

            function setMinStartTime() {
                const now = new Date();
                const today = now.toISOString().split('T')[0]; // Get 'YYYY-MM-DD'
                const selectedDate = reservationDateInput.val();

                if (selectedDate === today) {
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    startTimeInput.attr('min', `${hours}:${minutes}`);
                } else {
                    startTimeInput.removeAttr('min');
                }
            }

            // Set end time to be after start time
            startTimeInput.on('change', function() {
                endTimeInput.attr('min', startTimeInput.val());
                endTimeInput.val(''); // Clear end time to prevent conflicts
            });

            reservationDateInput.on('change', setMinStartTime);

            // When the modal is shown, apply the min time logic
            $('#addModal').on('shown.bs.modal', function() {
                setMinStartTime();
            });

            $("#start_time").on("change", function() {
                let startTime = $(this).val();
                $("#end_time").attr("min", startTime);
            });

            $("#end_time").on("change", function() {
                let startTime = $("#start_time").val();
                let endTime = $(this).val();

                if (endTime <= startTime) {
                    showErrorMessage("End Time must be greater than Start Time!");
                    $(this).val("");
                }
            });

            $('#addForm').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Collect reservation data from form
                const reservationData = {
                    'facility_id': $('#facility_id').val(),
                    'employee_id': $('#user_id').val(), // Correct key name for employee ID
                    'reservation_date': $('#reservation_date').val(),
                    'start_time': $('#start_time').val(),
                    'end_time': $('#end_time').val(),
                    'purpose': $('#purpose').val(),
                };

                $.ajax({
                    method: 'POST',
                    url: '/facilityReservations',
                    data: reservationData,
                    dataType: 'JSON',
                    cache: false,
                    success: function(response) {
                        if (response.valid) {
                            $('#addForm').trigger('reset'); // Reset the form
                            showSuccessMessage(response.msg); // Show success message
                            $('#addModal').modal('hide'); // Hide modal after success

                            // Refresh the calendar to reflect the new reservation
                            calendar.removeAllEvents(); // Clear all events
                            fetchEvents(); // Re-fetch and add updated events to the calendar
                        }
                    },
                    error: function(jqXHR) {
                        let errorMsg = "An unexpected error occurred. Please try again.";

                        if (jqXHR.responseJSON) {
                            if (jqXHR.responseJSON.errors) {
                                errorMsg = `${jqXHR.responseJSON.msg}\n`;
                                for (const [field, messages] of Object.entries(jqXHR
                                        .responseJSON.errors)) {
                                    errorMsg += `- ${messages.join(', ')}\n`;
                                }
                            } else if (jqXHR.responseJSON.msg) {
                                errorMsg = jqXHR.responseJSON.msg;
                            }
                        }

                        showErrorMessage(errorMsg);
                    }
                });
            });

            $('#updateForm').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                if (selectedReservation) {
                    // Prepare data to update reservation
                    const updateData = {
                        'facility_id': $('#facility_id').val(),
                        'employee_id': $('#user_id').val(), // Correct key for employee ID
                        'reservation_date': $('#updateForm').find('input[id=reservation_date]').val(),
                        'start_time': $('#updateForm').find('input[id=start_time]').val(),
                        'end_time': $('#updateForm').find('input[id=end_time]').val(),
                        'purpose': $('#updateForm').find('textarea[id=purpose]').val(),
                    };

                    $.ajax({
                        method: 'PUT',
                        url: `/facilityReservations/${selectedReservation.reservation_id}`,
                        data: updateData,
                        dataType: 'JSON',
                        cache: false,
                        success: function(response) {
                            if (response.valid) {
                                $('#updateForm').trigger('reset'); // Reset the form
                                showSuccessMessage(response.msg); // Show success message
                                $('#updateModal').modal('hide'); // Hide modal after success

                                // Refresh the calendar
                                calendar.removeAllEvents(); // Clear all events
                                fetchEvents
                                    (); // Re-fetch and add updated events to the calendar
                            }
                        },
                        error: function(jqXHR) {
                            let errorMsg = "An unexpected error occurred. Please try again.";

                            if (jqXHR.responseJSON) {
                                if (jqXHR.responseJSON.errors) {
                                    errorMsg = `${jqXHR.responseJSON.msg}\n`;
                                    for (const [field, messages] of Object.entries(jqXHR
                                            .responseJSON.errors)) {
                                        errorMsg += `- ${messages.join(', ')}\n`;
                                    }
                                } else if (jqXHR.responseJSON.msg) {
                                    errorMsg = jqXHR.responseJSON.msg;
                                }
                            }

                            showErrorMessage(
                                errorMsg); // Show validation errors or other error messages
                        }
                    });
                }
            });

            $('#reservationGuideModal').modal('show')

        });
    </script>
@endsection
