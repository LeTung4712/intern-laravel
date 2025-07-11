<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Models\Calculation;
use App\Mail\CalculationReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCalculationReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:calculation-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi mail kết quả tính toán trong 10 phút gần nhất cho tất cả người dùng';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $calculations = Calculation::where('created_at', '>=', now()->subMinutes(10))->get();

        Log::info("Đang gửi báo cáo phép tính trong 10 phút qua");

        if ($calculations->isEmpty()) {
            $this->info('Không có phép tính nào trong 10 phút qua.');
            return;
        }

        // Chỉ lấy những user đang active
        User::where('active', true)
            ->chunk(100, function ($users) use ($calculations) {
                foreach ($users as $user) {
                    Log::info("Đang gửi mail cho người dùng active: {$user->email}");
                    Mail::to($user->email)->queue(new CalculationReportMail($calculations, $user));
                }
            });

        $this->info('Đã gửi mail đến tất cả người dùng active.');
    }
}
