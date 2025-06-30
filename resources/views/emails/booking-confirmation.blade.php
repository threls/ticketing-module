@component('mail::message')
# Booking Confirmation

Dear {{$userName}}

Thank you for your purchase!

Here are your booking details:

- **Booking Reference:** {{ $bookingReference }}
- **Date:** {{ \Carbon\Carbon::parse($bookingDate)->format('d M, Y') }}

<br>
Your ticket(s) and payment receipt are attached to this email.

If you have any questions, feel free to contact our support at {{config('ticketing-module.support_email')}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
