@extends('frontend.layouts.app')

@section('title', 'Calculation History')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Calculation History</h4>
                        <div>
                            <a href="{{ route('calculator.index') }}" class="btn btn-secondary btn-sm">Back to Calculator</a>
                            <form action="{{ route('calculator.clear') }}" method="POST" class="d-inline"
                                id="clearHistoryForm">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm ml-2">Clear History</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
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
    </div>

    @push('after-scripts')
        <script>
            document.getElementById('clearHistoryForm').addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to clear all calculation history?')) {
                    e.preventDefault();
                }
            });
        </script>
    @endpush
@endsection
