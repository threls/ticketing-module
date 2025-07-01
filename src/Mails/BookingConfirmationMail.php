<?php

namespace Threls\ThrelsTicketingModule\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Threls\ThrelsTicketingModule\Dto\BookingConfirmationMailDto;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly BookingConfirmationMailDto $dto) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'ticketing-module::emails.booking-confirmation',
            with: $this->dto->toArray(),
        );
    }

    public function attachments(): array
    {

        return collect($this->dto->attachments)
            ->map(function ($attachment) {
                if ($attachment instanceof Media) {
                    return $attachment->toMailAttachment();
                }

                if (is_string($attachment) && filter_var($attachment, FILTER_VALIDATE_URL)) {
                    return Attachment::fromUrl($attachment);
                }

                return null;
            })
            ->all();
    }
}
