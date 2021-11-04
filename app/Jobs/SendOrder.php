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

    protected $order;

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

        $familyPath = $this->order->family->getFullPath();
        if ($this->order->full_family) {
            $message->attach($familyPath, $this->order->family->filename);
        } else {
            foreach ($this->order->styles as $styleName => $style) {
                $script = config('app.instancer');
                $instanceFilename = sprintf('%s-%s.ttf', $this->order->family->name, $styleName);
                $outputPath = tempnam(sys_get_temp_dir(), $instanceFilename);
                $args = collect($style)
                    ->map(function ($value, $key) {
                        return "$key=$value";
                    })
                    ->join(' ');
                `$script -o="$outputPath" "$familyPath" $args`;
                $message->attach($outputPath, [
                    'as' => $instanceFilename,
                ]);
            }
        }

        Mail::to($this->order->email)
            ->send($message);
    }
}
