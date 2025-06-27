@extends('adminlte::page')

@section('title', __('Create Appointment'))

@section('content_header')
    <h1>{{ __('Create Appointment') }}</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add new appointment') }}</h3>
            <a href="{{ route('appointments.index') }}"
                class="btn btn-sm btn-secondary float-right">{{ __('Back to list') }}</a>
        </div>

        <form action="{{ route('appointments.store') }}" method="POST" novalidate>
            @csrf
            <div class="card-body">

                @if (auth()->user()->type != 'patient')
                    <div class="form-group">
                        <label for="patient_id">{{ __('Patient') }}</label>
                        <div class="input-group">
                            <select name="patient_id" id="patient_id" class="form-control" required>
                                <option value="">{{ __('Choose patient') }}</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}"
                                        {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if (auth()->user()->type == 'admin')
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary" data-toggle="modal"
                                        data-target="#addUserModal" data-type="patient">
                                        {{ __('Add Patient') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <input type="hidden" name="patient_id" value="{{ auth()->user()->id }}">
                @endif

                @if (auth()->user()->type != 'doctor')
                    <div class="form-group">
                        <label for="doctor_id">{{ __('Doctor') }}</label>
                        <div class="input-group">
                            <select name="doctor_id" id="doctor_id" class="form-control" required>
                                <option value="">{{ __('Choose doctor') }}</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->user->name }} - {{ $doctor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            @if (auth()->user()->type == 'admin')
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary" data-toggle="modal"
                                        data-target="#addUserModal" data-type="doctor">
                                        {{ __('Add Doctor') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <input type="hidden" name="doctor_id" value="{{ auth()->user()->id }}">
                @endif

                <div class="form-group">
                    <label for="date">{{ __('Date') }}</label>
                    <input type="text" name="date" id="date"
                        class="form-control bg-white @error('date') is-invalid @enderror" value="{{ old('date') }}"
                        required>
                    @error('date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col">
                        <label for="start_time">{{ __('Start Time') }}</label>
                        <input type="text" name="start_time" id="start_time"
                            class="form-control bg-white @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="end_time">{{ __('End Time') }}</label>
                        <input type="text" name="end_time" id="end_time"
                            class="form-control bg-white @error('end_time') is-invalid @enderror"
                            value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if (auth()->user()->type != 'patient')
                    <div class="form-group">
                        <label for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                            required>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}
                            </option>
                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>
                                {{ __('Confirmed') }}</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                {{ __('Cancelled') }}</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                {{ __('Completed') }}</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <input type="hidden" name="status" value="pending">
                @endif

                <div class="form-group">
                    <label for="notes">{{ __('Notes') }}</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
            </div>
        </form>
    </div>

    {{-- New User Modal --}}
    @if (auth()->user()->type == 'admin')
        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="addUserForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserLabel"></h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="type" id="userType">

                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input type="text" name="name" class="form-control" required />
                            </div>

                            <div class="form-group">
                                <label for="email">{{ __('Email') }}</label>
                                <input type="email" name="email" class="form-control" required />
                            </div>

                            {{-- Doctor Fields --}}
                            <div id="doctorFields" style="display: none;">
                                <div class="form-group">
                                    <label for="specialization">{{ __('Specialization') }}</label>
                                    <input type="text" name="specialization" class="form-control" />
                                </div>

                                <div class="form-group">
                                    <label for="qualifications">{{ __('Qualifications') }}</label>
                                    <input type="text" name="qualifications" class="form-control" />
                                </div>

                                <div class="form-group">
                                    <label for="available_days">{{ __('Available Days') }}</label>
                                    <select name="available_days[]" class="form-control" multiple>
                                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                            <option value="{{ $day }}">{{ __($day) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="session_duration">{{ __('Session Duration (minutes)') }}</label>
                                    <input type="number" name="session_duration" class="form-control" min="5" />
                                </div>
                            </div>
                            {{-- End Doctor Fields --}}

                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <input type="password" name="password" class="form-control" required />
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" class="form-control" required />
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                {{ __('Save') }}
                            </button>
                            <button class="btn btn-primary d-none" type="button" id="loadingBtn" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

@stop


@section('js')
    <script>
        // START & END TIME PICKERS
        const startPicker = flatpickr("#start_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minTime: "09:00",
            maxTime: "17:00",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const startTime = selectedDates[0];
                    const endMin = new Date(startTime.getTime() + 15 * 60000);
                    const hours = String(endMin.getHours()).padStart(2, '0');
                    const minutes = String(endMin.getMinutes()).padStart(2, '0');
                    const minEndTime = `${hours}:${minutes}`;

                    endPicker.set("minTime", minEndTime);
                    endPicker.setDate(null);
                }
            }
        });

        const endPicker = flatpickr("#end_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minTime: "09:30",
            maxTime: "17:00",
        });

        // DATE PICKER
        const doctorDaysMap = @json($doctors);
        const currentUserType = "{{ auth()->user()->type }}";
        const currentDoctorId = {{ auth()->user()->id }};

        const dayNameToNumber = {
            Sunday: 0,
            Monday: 1,
            Tuesday: 2,
            Wednesday: 3,
            Thursday: 4,
            Friday: 5,
            Saturday: 6
        };

        let selectedDoctorId = null;

        let datePickerInstance = flatpickr("#date", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            minDate: "today",
            enable: [() => false],
            onOpen: () => {
                if (!selectedDoctorId) {
                    toastr.warning("{{ __('Please select a doctor first to choose a date.') }}");
                }
            },
            onReady: (selectedDates, dateStr, instance) => {
                instance.altInput.placeholder = "{{ __('Choose a date') }}";
            }
        });

        function enableDoctorDays(allowedDays) {
            const allowedDayNumbers = allowedDays.map(day => dayNameToNumber[day]);

            datePickerInstance.destroy();
            datePickerInstance = flatpickr("#date", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                minDate: "today",
                enable: [
                    function(date) {
                        return allowedDayNumbers.includes(date.getDay());
                    }
                ],
                onReady: (selectedDates, dateStr, instance) => {
                    instance.altInput.placeholder = "{{ __('Choose a date') }}";
                }
            });
        }


        if (currentUserType === 'doctor') {
            selectedDoctorId = currentDoctorId;

            const doctor = doctorDaysMap.find(d => d.user_id === selectedDoctorId);
            if (doctor && doctor.available_days) {
                const allowedDays = JSON.parse(doctor.available_days);
                enableDoctorDays(allowedDays);
            }
        }

        $('#doctor_id').on('change', function() {
            selectedDoctorId = parseInt($(this).val());
            $('#date').val('');

            if (!selectedDoctorId) {
                datePickerInstance.destroy();
                datePickerInstance = flatpickr("#date", {
                    altInput: true,
                    altFormat: "F j, Y",
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    enable: [() => false],
                    onOpen: () => toastr.warning(
                        "{{ __('Please select a doctor first to choose a date.') }}"),
                    onReady: instance => instance.altInput.placeholder = "{{ __('Choose a date') }}"
                });
                return;
            }

            const doctor = doctorDaysMap.find(d => d.id === selectedDoctorId);
            if (doctor && doctor.available_days) {
                const allowedDays = JSON.parse(doctor.available_days);
                enableDoctorDays(allowedDays);
            }
        });

        // ADD USER MODAL (Doctor / Patient)
        $(document).ready(function() {
            $('#addUserModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var userType = button.data('type');
                $('#userType').val(userType);
                $('#addUserLabel').text('Add ' + userType);
                $('#doctorFields').toggle(userType === 'doctor');
            });

            $('#addUserForm').submit(function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();

                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();

                $.ajax({
                    url: '{{ route('users.store') }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $('#submitBtn').addClass('d-none');
                        $('#loadingBtn').removeClass('d-none');
                    },
                    success: function(response) {
                        setTimeout(() => $('#addUserModal').modal('hide'), 100);
                        form[0].reset();

                        const user = response.user;
                        const newOption = new Option(user.name, user.id, true, true);
                        if (user.type === 'doctor') {
                            $('#doctor_id').append(newOption).val(user.id).trigger('change');
                        } else if (user.type === 'patient') {
                            $('#patient_id').append(newOption).val(user.id).trigger('change');
                        }

                        const typeLabel = user.type === "doctor" ?
                            @json(__('doctor')) :
                            @json(__('patient'));

                        toastr.success(
                            `The ${typeLabel} ${user.name} has been created successfully.`);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                let input = form.find('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.after('<div class="invalid-feedback">' + messages[
                                    0] + '</div>');
                            });
                        } else {
                            alert('Error: ' + xhr.responseText);
                        }
                    },
                    complete: function() {
                        $('#submitBtn').removeClass('d-none');
                        $('#loadingBtn').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endsection
