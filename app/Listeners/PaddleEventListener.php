<?php

namespace App\Listeners;

use App\Jobs\SendOrder;
use App\Mail\OrderMail;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Laravel\Paddle\Events\WebhookReceived;

class PaddleEventListener
{
    /**
     * Handle received Paddle webhooks.
     *
     * @param  \Laravel\Paddle\Events\WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        if ($event->payload['alert_name'] === 'payment_succeeded') {
            $orderId = $event->payload['passthrough'];

            $order = Order::findOrFail($orderId);
            $order->paid = true;
            $order->save();

            // SendOrder::dispatch($order);

            $message = new OrderMail($this->order);

            Mail::to($this->order->email)
                ->send($message);
        }
    }
}
