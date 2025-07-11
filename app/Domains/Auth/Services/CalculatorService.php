<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\Calculation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Services\BaseService;

class CalculatorService extends BaseService
{
    /**
     * Thực hiện phép tính và lưu kết quả
     *
     * @param float $number1
     * @param float $number2
     * @param string $operation
     * @return array
     * @throws InvalidArgumentException
     */
    public function calculate($number1, $number2, $operation)
    {
        $result = $this->performCalculation($number1, $number2, $operation);

        return $this->saveCalculation($number1, $number2, $operation, $result);
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
     * Lưu kết quả tính toán vào database
     *
     * @param float $number1
     * @param float $number2
     * @param string $operation
     * @param float $result
     * @return array
     */
    protected function saveCalculation($number1, $number2, $operation, $result)
    {
        $calculation = Calculation::create([
            'number1' => $number1,
            'number2' => $number2,
            'operation' => $operation,
            'result' => $result,
        ]);

        return [
            'calculation' => $calculation,
            'result' => $result
        ];
    }

    /**
     * Lấy lịch sử tính toán
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHistory($limit = 10)
    {
        return Calculation::latest()->take($limit)->get();
    }

    /**
     * Lấy lịch sử tính toán có phân trang
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getHistoryPaginated($perPage = 10)
    {
        return Calculation::latest()
            ->paginate($perPage);
    }

    /**
     * Xóa toàn bộ lịch sử tính toán
     *
     * @return void
     */
    public function clearHistory()
    {
        DB::transaction(function () {
            Calculation::truncate();
        });
    }
}
