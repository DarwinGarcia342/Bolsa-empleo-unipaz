<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Company $company) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Tu empresa ha sido aprobada en UNIPAZ Bolsa de Empleo!')
            ->greeting('¡Felicitaciones, ' . $notifiable->name . '!')
            ->line('Tu empresa **' . $this->company->company_name . '** ha sido verificada y aprobada por el equipo de UNIPAZ.')
            ->line('Ya puedes ingresar a tu panel y comenzar a publicar vacantes para los estudiantes de nuestra institución.')
            ->action('Publicar mi primera vacante', route('company.jobs.create'))
            ->salutation('Equipo UNIPAZ Bolsa de Empleo — Barrancabermeja');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'company_approved',
            'company_name' => $this->company->company_name,
            'message'      => 'Tu empresa "' . $this->company->company_name . '" ha sido aprobada. Ya puedes publicar vacantes.',
        ];
    }
}
