<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Threls\ThrelsTicketingModule\Dto\CustomTicketRestrictionItemDto;
use Threls\ThrelsTicketingModule\Dto\UpdateCustomTicketRestrictionDto;
use Threls\ThrelsTicketingModule\Models\CustomRestriction;
use Threls\ThrelsTicketingModule\Models\Ticket;

class UpdateCustomTicketRestrictionAction
{
    protected UpdateCustomTicketRestrictionDto $dto;

    protected CustomRestriction $restriction;

    public function execute(UpdateCustomTicketRestrictionDto $dto): void
    {
        $this->dto = $dto;
        $this->restriction = CustomRestriction::findOrFail($dto->customRestrictionId);

        $this->checkOverlappingRestrictions()
            ->updateCustomRestriction()
            ->updateRestrictionItems();

    }

    protected function checkOverlappingRestrictions(): static
    {
        $ticket = Ticket::findOrFail($this->restriction->ticket_id);

        $overlappingRestrictions = $ticket->customRestrictions()
            ->where('id', '!=', $this->restriction->id)
            ->whereBetween('from_date', [$this->dto->fromDate, $this->dto->toDate])
            ->orWhereBetween('to_date', [$this->dto->fromDate, $this->dto->toDate])
            ->orWhere(function ($query) {
                $query->whereDate('from_date', '<=', $this->dto->fromDate)
                    ->whereDate('to_date', '>=', $this->dto->toDate);
            })
            ->count();

        if ($overlappingRestrictions) {
            throw new BadRequestHttpException('Custom restrictions exists for the specified period.');
        }

        return $this;
    }

    protected function updateCustomRestriction(): static
    {
        $this->restriction->from_date = $this->dto->fromDate;
        $this->restriction->to_date = $this->dto->toDate;
        $this->restriction->save();

        return $this;
    }

    protected function updateRestrictionItems(): void
    {
        $this->dto->restrictions->each(function (CustomTicketRestrictionItemDto $restrictionItemDto) {
            $this->restriction->customRestrictionItems()->updateOrCreate(
                [
                    'ticket_restriction_id' => $restrictionItemDto->ticketRestrictionId,
                    'key' => $restrictionItemDto->key,
                ],
                [
                    'value' => $restrictionItemDto->value,
                ]
            );
        });

        $this->restriction->customRestrictionItems()->whereNotIn('ticket_restriction_id', $this->dto->restrictions->pluck('ticket_restriction_id')->toArray())->delete();

    }
}
