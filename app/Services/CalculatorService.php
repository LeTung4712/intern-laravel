<?php

namespace App\Services;

use App\Domains\Auth\Models\Calculation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Services\BaseService;

class CalculatorService extends BaseService
{

    public function calculate($number1, $number2, $operation)
    {
        return $this->saveCalculation($number1, $number2, $operation);
    }

    /**
     * Lưu phép tính vào database để xử lý
     *
     * @param float $number1
     * @param float $number2
     * @param string $operation
     * @return array
     */
    protected function saveCalculation($number1, $number2, $operation)
    {
        $calculation = Calculation::create([
            'number1' => $number1,
            'number2' => $number2,
            'operation' => $operation,
            'result' => null // Kết quả sẽ được cập nhật bởi observer
        ]);

        return [
            'calculation' => $calculation,
            'message' => 'Đang xử lý phép tính của bạn...'
        ];
    }

    /**
     * Lấy lịch sử tính toán có phân trang
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getHistoryPaginated($perPage = 5)
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
