<?php

namespace App\Observers;

use App\Models\Calculation;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;

class CalculationObserver
{
    /**
     * Handle the Calculation "created" event.
     *
     * @param  \App\Models\Calculation  $calculation
     * @return void
     */
    public function created(Calculation $calculation)
    {
        try {
            $result = $this->performCalculation(
                $calculation->number1,
                $calculation->number2,
                $calculation->operation
            );

            $calculation->result = $result;
            $calculation->save();

            session()->flash('success', 'Kết quả: ' . $result);
        } catch (\Exception $e) {
            // Log lỗi nếu cần
            Log::error('Calculation error: ' . $e->getMessage());
        }
    }

    /**
     * Thực hiện phép tính
     *
     * @param float $number1
     * @param float $number2
     * @param string $operation
     * @return float
     * @throws InvalidArgumentException
     */
    protected function performCalculation($number1, $number2, $operation)
    {
        return match ($operation) {
            '+' => $number1 + $number2,
            '-' => $number1 - $number2,
            '*' => $number1 * $number2,
            '/' => $this->divide($number1, $number2),
            default => throw new InvalidArgumentException('Invalid operation'),
        };
    }

    /**
     * Thực hiện phép chia
     *
     * @param float $number1
     * @param float $number2
     * @return float
     * @throws InvalidArgumentException
     */
    protected function divide($number1, $number2)
    {
        if ($number2 == 0) {
            throw new InvalidArgumentException('Cannot divide by zero.');
        }
        return $number1 / $number2;
    }

    /**
     * Handle the Calculation "updated" event.
     *
     * @param  \App\Models\Calculation  $calculation
     * @return void
     */
    public function updated(Calculation $calculation)
    {
        //
    }

    /**
     * Handle the Calculation "deleted" event.
     *
     * @param  \App\Models\Calculation  $calculation
     * @return void
     */
    public function deleted(Calculation $calculation)
    {
        //
    }

    /**
     * Handle the Calculation "restored" event.
     *
     * @param  \App\Models\Calculation  $calculation
     * @return void
     */
    public function restored(Calculation $calculation)
    {
        //
    }

    /**
     * Handle the Calculation "force deleted" event.
     *
     * @param  \App\Models\Calculation  $calculation
     * @return void
     */
    public function forceDeleted(Calculation $calculation)
    {
        //
    }
}
