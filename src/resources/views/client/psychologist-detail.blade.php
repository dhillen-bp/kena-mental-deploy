@extends('layouts.client')
@section('title', 'Psychologist Detail')

@section('client_content')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>

    <div class="card mx-auto my-5" style="max-width: 540px;">
        <div class="row g-0">
            <div class="col-md-4 bg-purple">
                @if ($psychologist->photo != '')
                    <img src="https://storage.googleapis.com/kenamental_bucket/images/psychologist_photo/{{ $psychologist->photo }}"
                        style="width: 200px; height: 100%; object-fit: cover;">
                @else
                    <img src="https://storage.googleapis.com/kenamental_bucket/images/psychologist_photo/default.png"
                        alt="Photo" width="200px">
                @endif
            </div>
            <div class="col-md-8 bg-light">
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $psychologist->name }}</h5>
                    <p class="card-text">{{ $psychologist->biography }}.</p>
                    <p class="card-text">{{ optional($psychologist->psychologistDetail)->university }} <small
                            class="card-text text-body-secondary d-block text-sm">{{ optional($psychologist->psychologistDetail)->degree }}
                            -
                            {{ optional($psychologist->psychologistDetail)->year }}</small></p>

                    <p class="card-text">Topics: {{ optional($psychologist->psychologistDetail)->topics }}</p>
                    <h6>Consult Session: <span class="badge bg-purple"><i class="bi bi-person-hearts"></i>
                            {{ optional($psychologist->psychologistDetail)->session_count }}</span></h6>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="/psychologists" class="btn btn-secondary">Back</a>
                <a href="/form-consultation/{{ $psychologist->id }}" class="btn btn-purple">Consult Now</a>
            </div>
        </div>
    </div>
    <div id="calendar"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    @foreach ($psychologist->consultations as $consultation)
                        {
                            @if (optional($consultation->paymentConsultation)->status === 'paid')
                                title: 'Consultation',
                                start: '{{ $consultation->booking_date }}',
                                // end: '{{ $consultation->booking_date }}',
                            @endif

                        },
                    @endforeach
                ]
            });
            calendar.render();
        });
    </script>
@endsection
