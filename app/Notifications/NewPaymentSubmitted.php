<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class NewPaymentSubmitted extends Notification
{
    use Queueable;

    public $payment;

    // This "constructor" allows us to pass the payment data into the notification
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    // We only want to save this to the database for the bell icon
    public function via($notifiable)
    {
        return ['database'];
    }

    // This is the data that will be stored in your 'notifications' table
    public function toArray($notifiable)
    {
        return [
            'payment_id'  => $this->payment->id,
            'amount'      => $this->payment->amount,
            'tenant_name' => auth()->user()->name,
            'message'     => 'New payment of ₱' . number_format($this->payment->amount, 2) . ' submitted by ' . auth()->user()->name,
        ];
    }
}