<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $student    = $this->application->user;
        $jobPosting = $this->application->jobPosting;

        return (new MailMessage)
            ->subject('Nueva postulación en "' . $jobPosting->title . '"')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('El estudiante **' . $student->name . '** se ha postulado a tu vacante:')
            ->line('**Vacante:** ' . $jobPosting->title)
            ->line('**Correo del estudiante:** ' . $student->email)
            ->action('Ver postulación', route('company.applications'))
            ->line('Ingresa al panel de UNIPAZ Bolsa de Empleo para revisar su perfil y hoja de vida.')
            ->salutation('Equipo UNIPAZ Bolsa de Empleo');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'new_application',
            'application_id'   => $this->application->id,
            'student_name'     => $this->application->user->name,
            'job_posting_title'=> $this->application->jobPosting->title,
            'message'          => $this->application->user->name . ' se postuló a "' . $this->application->jobPosting->title . '"',
        ];
    }
}
