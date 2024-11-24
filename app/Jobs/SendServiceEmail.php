<?php

namespace App\Jobs;

use App\Mail\AddService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendServiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $serviceName;
    /**
     * Create a new job instance.
     */
    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user_emails = User::pluck('email');
        foreach ($user_emails as $email) {
            Mail::to($email)->send(new AddService($this->serviceName));
        }
    }
}