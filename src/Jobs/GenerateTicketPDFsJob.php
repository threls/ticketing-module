<?php

namespace Threls\ThrelsTicketingModule\Jobs;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Threls\ThrelsTicketingModule\Events\TicketPdfsGeneratedEvent;
use Threls\ThrelsTicketingModule\Models\Booking;
use Throwable;

class GenerateTicketPDFsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking) {}

    public function handle(): void
    {
        if ($this->booking->bookingTickets()->count() == 0) {
            $this->fail(new BadRequestHttpException('There are no tickets generated for this booking.'));
        }

        $pdfBuilder = Pdf::withBrowsershot(function (Browsershot $browsershot) {
            if (! app()->environment('local')) {
                $browsershot
                    ->setChromePath(config('ticketing-module.chrome_path')) // Use manually installed Chromium
                    ->setCustomTempPath(storage_path('temp'))    // Custom temp directory for server compatibility
                    ->setOption('executablePath', config('ticketing-module.chrome_path'))
                    ->setOption('args', ['--no-sandbox'])                       // Disable sandbox for headless Chromium compatibility
                    ->newHeadless();
            } else {
                $browsershot->setNodeBinary( config('ticketing-module.node_binary_path', '/opt/homebrew/bin/node'));
            }
        });

        $this->booking->load('bookingTickets.bookingItem.event', 'bookingTickets.ticket', 'bookingClient');

        Bus::batch(
            $this->booking->bookingTickets->map(fn ($ticket) =>
            new GenerateSingleTicketPDFJob($this->booking,$ticket,$pdfBuilder)
            )
        )->catch(function (Batch $batch, Throwable $e) {
            $this->fail(new BadRequestHttpException(new BadRequestHttpException('Batch with id '.$batch->id.' did not generate tickets PDFs.')));
        })->then(function () {
            TicketPdfsGeneratedEvent::dispatch($this->booking);
        })->dispatch();

    }
}
