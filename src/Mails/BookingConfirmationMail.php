<?php

namespace Threls\ThrelsTicketingModule\Mails;

use Illuminate\Bus\Queueable;
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
            markdown: 'emails.booking-confirmation',
            with: $this->dto->toArray(),
        );
    }

    public function attachments(): array
    {
        return $this->dto->attachments
            ->map(fn (Media $media) => $media->toMailAttachment())
            ->all();
    }
}
