<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\CreateTicketDto;
use Threls\ThrelsTicketingModule\Models\Event;
use Threls\ThrelsTicketingModule\Models\Ticket;
use Threls\ThrelsTicketingModule\Models\VatRate;

class CreateTicketAction
{
    protected CreateTicketDto $createTicketDto;

    protected Event $event;

    protected Ticket $ticket;

    public function __construct(
        protected readonly ExtractVatAction $extractVatAction,
    ) {}

    public function execute(CreateTicketDto $dto): Ticket
    {
        $this->createTicketDto = $dto;
        $this->event = $dto->event;

        $this->crateTicket()
            ->createTicketRestrictions();

        return $this->ticket;
    }

    protected function crateTicket(): static
    {
        /** @var VatRate $vat */
        $vat = VatRate::find($this->createTicketDto->vatId);

        $this->ticket = $this->event->tickets()->create([
            'parent_id' => $this->createTicketDto->dependentOfId,
            'name' => $this->createTicketDto->name,
            'pax_number' => $this->createTicketDto->paxNumber,
            'price' => $this->createTicketDto->price,
            'price_currency' => $this->createTicketDto->currency,
            'vat_id' => $this->createTicketDto->vatId,
            'vat_amount' => $this->extractVatAction->execute($this->createTicketDto->price, $vat->rate),
            'vat_amount_currency' => $this->createTicketDto->currency,
            'status' => $this->createTicketDto->status,
        ]);

        return $this;
    }

    protected function createTicketRestrictions(): void
    {
        $this->ticket->restrictions()->createMany($this->createTicketDto->restrictions->toArray());

    }
}
