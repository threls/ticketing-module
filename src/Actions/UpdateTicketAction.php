<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\TicketRestrictionDto;
use Threls\ThrelsTicketingModule\Dto\UpdateTicketDto;
use Threls\ThrelsTicketingModule\Models\VatRate;

class UpdateTicketAction
{
    protected UpdateTicketDto $dto;

    public function __construct(
        protected readonly ExtractVatAction $extractVatAction,
    )
    {
    }
    public function execute(UpdateTicketDto $dto)
    {
        $this->dto = $dto;

        $this->updateTicket()
            ->updateTicketRestrictions();

        return $this->dto->ticket->fresh();
    }

    protected function updateTicket(): static
    {
        /** @var VatRate $vat */
        $vat = VatRate::find($this->dto->vatId);

        $this->dto->ticket->update([
            'parent_id' => $this->dto->dependentOfId,
            'name' => $this->dto->name,
            'pax_number' => $this->dto->paxNumber,
            'price' => $this->dto->price,
            'currency' => $this->dto->currency,
            'vat_id' => $this->dto->vatId,
            'vat_amount' => $this->extractVatAction->execute($this->dto->price, $vat->rate),
            'status' => $this->dto->status,
        ]);

        return $this;
    }

    protected function updateTicketRestrictions(): void
    {
        $this->dto->restrictions->each(function (TicketRestrictionDto $restrictionDto) {
            $this->dto->ticket->restrictions()->updateOrCreate(
                ['key' => $restrictionDto->key],
                ['value' => $restrictionDto->value]);
        });

        $this->dto->ticket->restrictions()->whereNotIn('key', $this->dto->restrictions->pluck('key')->toArray())->delete();
    }

}
