<?php

namespace App\Listeners;

use App\Jobs\SendOrder;
use App\Models\Order;
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
            $orderId = $event->payload['passthrough']->orderId;

            $order = Order::find($orderId);
            $order->paid = true;
            $order->save();

            SendOrder::dispatch($order);
        }
    }
}
