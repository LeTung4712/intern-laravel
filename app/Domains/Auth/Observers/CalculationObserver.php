<?php

namespace App\Domains\Auth\Observers;

use App\Domains\Auth\Models\Calculation;
use App\Domains\Auth\Jobs\SendCalculationResultJob;

class CalculationObserver
{

    public function created(Calculation $calculation)
    {
        SendCalculationResultJob::dispatch($calculation);
    }


    public function updated(Calculation $calculation)
    {
        //
    }


    public function deleted(Calculation $calculation)
    {
        //
    }


    public function restored(Calculation $calculation)
    {
        //
    }


    public function forceDeleted(Calculation $calculation)
    {
        //
    }
}
