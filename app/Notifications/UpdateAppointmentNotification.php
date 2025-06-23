<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class UpdateAppointmentNotification extends Notification
{
    protected $appointment;

    protected $type;

    public function __construct($appointment, $type)
    {
        $this->appointment = $appointment;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        if($this->type == 'status') {
            $message = 'Your Appointment with ' . $this->appointment->doctor->user->name . ' ' . $this->appointment->status;
        } else {
            $message = 'Your Appointment with ' . $this->appointment->doctor->user->name . ' has been updated';
        }
        
        return [
            'icon'  => 'fas fa-calendar-alt text-primary',
            'title' => 'Appointment',
            'body'  => $message,
            'url'   => route('appointments.show', $this->appointment->id),
        ];
    }
}
