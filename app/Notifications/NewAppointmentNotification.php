<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
{
    protected $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'icon' => 'fas fa-calendar-alt text-primary',
            'title' => 'New Appointment',
            'body' => 'Appointment with ' . $this->appointment->patient->name,
            'url' => route('appointments.show', $this->appointment->id),
        ];
    }
}
