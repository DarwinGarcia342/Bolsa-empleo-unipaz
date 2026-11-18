<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jobPosting = $this->application->jobPosting;
        $company    = $jobPosting->company;
        $status     = $this->application->status_label;

        $message = (new MailMessage)
            ->subject('Actualización de tu postulación en ' . $company->company_name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('La empresa **' . $company->company_name . '** ha actualizado el estado de tu postulación a:')
            ->line('**Vacante:** ' . $jobPosting->title)
            ->line('**Nuevo estado:** ' . $status);

        if ($this->application->status === 'interview') {
            $message->line('¡Felicitaciones! Has sido seleccionado para una entrevista. La empresa te contactará pronto.')
                    ->action('Ver mis postulaciones', route('student.applications'));
        } elseif ($this->application->status === 'accepted') {
            $message->line('¡Excelentes noticias! Has sido seleccionado para este cargo. La empresa se pondrá en contacto contigo.')
                    ->action('Ver mis postulaciones', route('student.applications'));
        } elseif ($this->application->status === 'rejected') {
            $message->line('Lamentamos informarte que en esta ocasión no fuiste seleccionado. Te animamos a seguir explorando otras oportunidades.')
                    ->action('Ver más vacantes', route('student.jobs'));
        } else {
            $message->action('Ver mis postulaciones', route('student.applications'));
        }

        return $message->salutation('Equipo UNIPAZ Bolsa de Empleo');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'              => 'application_status',
            'application_id'    => $this->application->id,
            'job_posting_title' => $this->application->jobPosting->title,
            'company_name'      => $this->application->jobPosting->company->company_name,
            'status'            => $this->application->status,
            'message'           => 'Tu postulación en "' . $this->application->jobPosting->title . '" cambió a: ' . $this->application->status_label,
        ];
    }
}
