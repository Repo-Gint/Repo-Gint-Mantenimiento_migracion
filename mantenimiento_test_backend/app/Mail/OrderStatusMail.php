<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $pdfContent; // Nueva variable para recibir el PDF al vuelo

    public function __construct($data, $pdfContent = null)
    {
        $this->data = $data;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        $mail = $this->subject("Actualización de Orden: {$this->data['folio']} - {$this->data['status']}")
                     ->view('emails.order_status');

        if ($this->pdfContent) {
            $mail->attachData($this->pdfContent, 'Resolucion_' . $this->data['folio'] . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}