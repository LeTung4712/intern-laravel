@extends('frontend.layouts.app')

@section('title', 'Calculator')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Calculator</h4>
                    </div>
                    <div class="card-body">
                        @if (session('info'))
                            <div class="alert alert-info">
                                {{ session('info') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('calculator.calculate') }}" method="POST" id="calculatorForm">
                            @csrf
                            <div class="form-group row mb-3">
                                <label for="number1" class="col-sm-3 col-form-label">First Number</label>
                                <div class="col-sm-9">
                                    <input type="number" step="any" class="form-control" id="number1" name="number1"
                                        required value="{{ old('number1') }}">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="number2" class="col-sm-3 col-form-label">Second Number</label>
                                <div class="col-sm-9">
                                    <input type="number" step="any" class="form-control" id="number2" name="number2"
                                        required value="{{ old('number2') }}">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="operation" class="col-sm-3 col-form-label">Operation</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="operation" name="operation" required>
                                        <option value="">Select Operation</option>
                                        <option value="+" {{ old('operation') == '+' ? 'selected' : '' }}>Addition (+)
                                        </option>
                                        <option value="-" {{ old('operation') == '-' ? 'selected' : '' }}>Subtraction
                                            (-)</option>
                                        <option value="*" {{ old('operation') == '*' ? 'selected' : '' }}>
                                            Multiplication (*)</option>
                                        <option value="/" {{ old('operation') == '/' ? 'selected' : '' }}>Division (/)
                                        </option>
                                    </select>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary">Calculate</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Calculation History</h4>
                    <form action="{{ route('calculator.clear') }}" method="POST" class="d-inline" id="clearHistoryForm"
                        onsubmit="return confirm('Are you sure you want to clear all history?');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Clear History</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>First Number</th>
                                    <th>Operation</th>
                                    <th>Second Number</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($calculations as $calc)
                                    <tr>
                                        <td>{{ $calc->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $calc->number1 }}</td>
                                        <td>{{ $calc->operation }}</td>
                                        <td>{{ $calc->number2 }}</td>
                                        <td>{{ $calc->result }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No calculations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $calculations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
