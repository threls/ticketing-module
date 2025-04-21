@component('mail::message')
# Booking Confirmation

Dear {{$userName}}

Thank you for your purchase!

Here are your booking details:

@component('mail::panel')
- **Booking Reference:** {{ $bookingId }}
- **Date:** {{ $bookingDate->format('d M, Y') }}
- **Number of People:** {{ $peopleNr }}
@endcomponent

@component('mail::panel')
    Your ticket(s) and payment receipt are attached to this email.
@endcomponent


If you have any questions, feel free to contact our support at {{config('ticketing-module.support_email')}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
