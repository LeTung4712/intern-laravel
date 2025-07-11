<?php

namespace App\Domains\Auth\Http\Controllers\Frontend;

use App\Domains\Auth\Services\CalculatorService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CalculatorController
{
    protected $calculatorService;

    public function __construct(CalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'number1' => 'required|numeric',
            'number2' => 'required|numeric',
            'operation' => 'required|in:+,-,*,/',
        ]);

        try {
            $result = $this->calculatorService->calculate(
                $validated['number1'],
                $validated['number2'],
                $validated['operation']
            );

            return response()->json($result);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function history(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $calculations = $this->calculatorService->getHistoryPaginated($perPage);
            return response()->json($calculations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve calculation history.'], 500);
        }
    }

    public function clearHistory()
    {
        try {
            $this->calculatorService->clearHistory();
            return response()->json(['message' => 'Calculation history cleared.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to clear calculation history.'], 500);
        }
    }
}
