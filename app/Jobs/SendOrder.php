<?php

namespace App\Jobs;

use App\Mail\OrderMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = new OrderMail($this->order);

        $file = $this->family->getFullPath();
        if ($this->order->full_family) {
            $message->attach($file, $this->family->name, [
                'mime' => 'font/ttf',
            ]);
        } else {
            foreach ($this->order->styles as $style) {
                $instance = `python generate_instance.py`;
                $message->attachData($instance, $style, [
                    'mime' => 'font/ttf',
                ]);
            }
        }

        Mail::to($this->order->email)
            ->send(new OrderMail($this->order));
    }
}
