<?php

namespace App\Domains\Auth\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\CalculationReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Models\Calculation;

class SendCalculationResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $calculation;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Calculation $calculation)
    {
        $this->calculation = $calculation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Processing calculation result for calculation ID: {$this->calculation->id}");

        // Gửi mail cho từng user đang active
        User::where('active', true)
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    try {
                        Log::info("Sending calculation result to user: {$user->email}");
                        Mail::to($user->email)->send(new CalculationReportMail(collect([$this->calculation]), $user));
                    } catch (\Exception $e) {
                        Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
                    }
                }
            });

        Log::info('SendCalculationResultJob completed');
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('SendCalculationResultJob failed: ' . $exception->getMessage());
    }
}
