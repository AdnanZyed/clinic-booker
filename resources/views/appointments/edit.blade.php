@extends('adminlte::page')

@section('title', __('Edit Appointment'))

@section('content_header')
    <h1>{{ __('Edit Appointment') }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Edit appointment') }}</h3>
            <a href="{{ route('appointments.index') }}"
                class="btn btn-sm btn-secondary float-right">{{ __('Back to list') }}</a>
        </div>

        <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">

                @if(auth()->user()->type != 'patient')
                    <div class="form-group">
                        <label for="patient_id">{{ __('Patient') }}</label>
                        <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror"
                            required>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}"
                                    {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <input type="hidden" name="patient_id" value="{{ auth()->user()->id }}">
                @endif

                @if(auth()->user()->type != 'doctor')
                    <div class="form-group">
                        <label for="doctor_id">{{ __('Doctor') }}</label>
                        <select name="doctor_id" id="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror"
                            required>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}"
                                    {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->user->name }} - {{ $doctor->specialization }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <input type="hidden" name="doctor_id" value="{{ auth()->user()->id }}">
                @endif

                <div class="form-group">
                    <label for="date">{{ __('Date') }}</label>
                    <input type="text" name="date" id="date"
                        class="form-control bg-white @error('date') is-invalid @enderror"
                        value="{{ old('date', $appointment->date) }}" required>
                    @error('date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col">
                        <label for="start_time">{{ __('Start Time') }}</label>
                        <input type="text" name="start_time" id="start_time"
                            class="form-control bg-white @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time', \Carbon\Carbon::parse($appointment->start_time)->format('H:i')) }}"
                            required>
                        @error('start_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="end_time">{{ __('End Time') }}</label>
                        <input type="text" name="end_time" id="end_time"
                            class="form-control bg-white @error('end_time') is-invalid @enderror"
                            value="{{ old('end_time', \Carbon\Carbon::parse($appointment->end_time)->format('H:i')) }}"
                            required>
                        @error('end_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if(auth()->user()->type != 'patient')
                    <div class="form-group">
                        <label for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                            required>
                            @foreach (['pending', 'confirmed', 'cancelled', 'completed'] as $status)
                                <option value="{{ $status }}" {{ $appointment->status === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
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
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
    const doctorDaysMap = @json($doctors);
    const appointment = @json($appointment);
    const currentDoctorId = {{ auth()->user()->type === 'doctor' ? auth()->user()->doctor->id : $appointment->doctor_id }};

    const dayNameToNumber = {
        Sunday: 0,
        Monday: 1,
        Tuesday: 2,
        Wednesday: 3,
        Thursday: 4,
        Friday: 5,
        Saturday: 6,
    };

    let datePickerInstance = null;

    function enableDoctorDays(allowedDays) {
        const allowedDayNumbers = allowedDays.map(day => dayNameToNumber[day]);

        if (datePickerInstance) {
            datePickerInstance.destroy();
        }

        datePickerInstance = flatpickr("#date", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            minDate: "today",
            defaultDate: appointment.date,
            enable: [date => allowedDayNumbers.includes(date.getDay())],
            onReady: instance => instance.altInput.placeholder = "{{ __('Choose a date') }}"
        });
    }

    const doctor = doctorDaysMap.find(d => d.id === currentDoctorId);
    if (doctor && doctor.available_days) {
        const allowedDays = JSON.parse(doctor.available_days);
        enableDoctorDays(allowedDays);
    }

    const startPicker = flatpickr("#start_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minTime: "09:00",
        maxTime: "17:00",
        defaultDate: appointment.start_time,
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                const startTime = selectedDates[0];
                const minEnd = new Date(startTime.getTime() + 30 * 60000);
                const hours = String(minEnd.getHours()).padStart(2, '0');
                const minutes = String(minEnd.getMinutes()).padStart(2, '0');
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
        defaultDate: appointment.end_time
    });
</script>
@endsection