<?php

namespace App\Http\Controllers;

use App\Http\Request\CalculateRequest;
use App\Services\ProfitCalculatorService;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function index(): View
    {
        return view('calculator.index');
    }

    public function calculate(
        CalculateRequest $request,
        ProfitCalculatorService $profitCalculatorService,
        ): View {
        $result = $profitCalculatorService->calculate($request->validated());

        return view('calculator.index', compact('result'));
    }
}
