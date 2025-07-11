<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CalculatorService;
use InvalidArgumentException;

class CalculatorController extends Controller
{
    protected $calculatorService;

    public function __construct(CalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    /**
     * Hiển thị form tính toán
     */
    public function index()
    {
        $perPage = request()->get('per_page', 10);
        $calculations = $this->calculatorService->getHistoryPaginated($perPage);
        return view('calculator.index', compact('calculations'));
    }

    /**
     * Xử lý tính toán
     */
    public function calculate(Request $request)
    {
        // Validate input
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

            return redirect()->route('calculator.index')
                ->with('info', $result['message']);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('calculator.index')
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->route('calculator.index')
                ->withInput()
                ->withErrors(['error' => 'An error occurred while processing your request.']);
        }
    }

    /**
     * Lấy lịch sử tính toán
     */
    public function history(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $calculations = $this->calculatorService->getHistoryPaginated($perPage);

            if ($request->wantsJson()) {
                return response()->json($calculations);
            }

            return view('calculator.history', compact('calculations'));
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to retrieve calculation history.'], 500);
            }
            return back()->withErrors(['error' => 'Failed to retrieve calculation history.']);
        }
    }

    /**
     * Xóa lịch sử tính toán
     */
    public function clearHistory(Request $request)
    {
        try {
            $this->calculatorService->clearHistory();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Calculation history cleared.']);
            }

            return back()->with('success', 'Calculation history cleared.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to clear calculation history.'], 500);
            }
            return back()->withErrors(['error' => 'Failed to clear calculation history.']);
        }
    }
}
