<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Threls\ThrelsTicketingModule\Dto\AddCustomTicketRestrictionDto;
use Threls\ThrelsTicketingModule\Dto\CustomTicketRestrictionItemDto;
use Threls\ThrelsTicketingModule\Models\CustomRestriction;
use Threls\ThrelsTicketingModule\Models\Ticket;

class AddCustomTicketRestrictionAction
{
    protected AddCustomTicketRestrictionDto $dto;
    protected CustomRestriction $restriction;

    public function execute(AddCustomTicketRestrictionDto $dto): void
    {
        $this->dto = $dto;

        $this->checkOverlappingRestrictions()
            ->createCustomRestriction()
            ->createRestrictionItems();

    }

    protected function checkOverlappingRestrictions(): static
    {
        $ticket = Ticket::findOrFail($this->dto->ticketId);

        $overlappingRestrictions = $ticket->customRestrictions()
            ->whereBetween('from_date', [$this->dto->fromDate, $this->dto->toDate])
            ->orWhereBetween('to_date', [$this->dto->fromDate, $this->dto->toDate])
            ->orWhere(function ($query) {
                $query->whereDate('from_date', '<=', $this->dto->fromDate)
                    ->whereDate('to_date', '>=', $this->dto->toDate);
            })->count();

        if ($overlappingRestrictions) {
            throw new BadRequestHttpException('Custom restrictions exists for the specified period.');
        }

        return $this;

    }

    protected function createCustomRestriction(): static
    {
        $customRestriction = new CustomRestriction();
        $customRestriction->ticket_id = $this->dto->ticketId;
        $customRestriction->from_date = $this->dto->fromDate;
        $customRestriction->to_date = $this->dto->toDate;
        $customRestriction->save();

        $this->restriction = $customRestriction;

        return $this;
    }

    protected function createRestrictionItems(): void
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

        $this->restriction->customRestrictionItems()->whereNotIn('ticket_restriction_id' , $this->dto->restrictions->pluck('ticket_restriction_id')->toArray())->delete();

    }

}
