<?php

namespace App\Listeners;

use App\Events\PaymentCreated;

class UpdateInvoicePaidAmountAndStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentCreated $event): void
    {
        $payment = $event->payment;
        
        // تحديث المبلغ المدفوع والحالة للفاتورة
        if ($payment->invoice) {
            $payment->invoice->updatePaidAmountAndStatus();
        }
    }
}
