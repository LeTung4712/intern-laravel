<div class="table-responsive">
    <!-- tạo giao diện  tính toán 2 số-->
    <form id="calculator-form" action="{{ route('frontend.auth.calculator') }}" method="POST">
        @csrf
        <div class="form-group row">
            <label for="number1" class="col-md-3 col-form-label text-md-right">@lang('Number 1')</label>
            <div class="col-md-9">
                <input type="number" name="number1" class="form-control" placeholder="{{ __('Enter first number') }}"
                    required>
            </div>
        </div><!--form-group-->
        <div class="form-group row">
            <label for="number2" class="col-md-3 col-form-label text-md-right">@lang('Number 2')</label>
            <div class="col-md-9">
                <input type="number" name="number2" class="form-control" placeholder="{{ __('Enter second number') }}"
                    required>
            </div>
        </div><!--form-group-->
        <div class="form-group row">
            <label for="operation" class="col-md-3 col-form-label text-md-right">@lang('Operation')</label>
            <div class="col-md-9">
                <select name="operation" class="form-control" required>
                    <option value="+">@lang('+')</option>
                    <option value="-">@lang('-')</option>
                    <option value="*">@lang('x')</option>
                    <option value="/">@lang('%')</option>
                </select>
            </div>
        </div><!--form-group-->
        <div class="form-group row mb-0">
            <div class="col-md-12 text-right">
                <button id="btn-submit" class="btn btn-sm btn-primary float-right"
                    type="button">@lang('Calculate')</button>
            </div>
        </div><!--form-group-->
    </form>

    <div id="result-container" class="alert alert-success mt-3" style="display: none;"></div>
    <div id="error-container" class="alert alert-danger mt-3" style="display: none;"></div>

    <div id="history-container" class="mt-4">
        <h5>@lang('Calculation History')</h5>
        <button id="clear-history-btn" class="btn btn-danger btn-sm mb-2" type="button">
            @lang('Clear History')
        </button>

        <div id="history-loading" class="text-center my-2" style="display: none;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            @lang('Loading...')
        </div>
        <ul id="history-list" class="list-group mb-3"></ul>
        <nav id="pagination-container" aria-label="History pagination" class="d-flex justify-content-center">
            <ul class="pagination pagination-sm"></ul>
        </nav>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('calculator-form');
            const resultDiv = document.getElementById('result-container');
            const errorDiv = document.getElementById('error-container');
            const historyList = document.getElementById('history-list');
            const historyLoading = document.getElementById('history-loading');
            const clearBtn = document.getElementById('clear-history-btn');
            const submitBtn = document.getElementById('btn-submit');
            const paginationContainer = document.querySelector('#pagination-container .pagination');
            let currentPage = 1;

            function loadHistory(page = 1) {
                historyLoading.style.display = 'block';
                historyList.innerHTML = '';
                paginationContainer.innerHTML = '';

                fetch(`{{ route('frontend.auth.calculator.history') }}?page=${page}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(response => {
                        historyLoading.style.display = 'none';

                        if (response.data.length === 0) {
                            historyList.innerHTML =
                                "<li class='list-group-item'>{{ __('No history found.') }}</li>";
                            return;
                        }

                        response.data.forEach(item => {
                            let opSymbol = '';
                            switch (item.operation) {
                                case '+':
                                    opSymbol = '+';
                                    break;
                                case '-':
                                    opSymbol = '-';
                                    break;
                                case '*':
                                    opSymbol = '×';
                                    break;
                                case '/':
                                    opSymbol = '÷';
                                    break;
                            }
                            const li = document.createElement('li');
                            li.classList.add('list-group-item');
                            li.textContent =
                                `${item.number1} ${opSymbol} ${item.number2} = ${item.result}`;
                            historyList.appendChild(li);
                        });

                        // Render pagination
                        renderPagination(response);
                    })
                    .catch(() => {
                        historyLoading.style.display = 'none';
                        historyList.innerHTML =
                            "<li class='list-group-item text-danger'>{{ __('Failed to load history.') }}</li>";
                    });
            }

            function renderPagination(response) {
                if (response.total <= response.per_page) return;

                const totalPages = Math.ceil(response.total / response.per_page);
                currentPage = response.current_page;

                // Previous button
                if (currentPage > 1) {
                    addPaginationButton('‹', currentPage - 1);
                }

                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    if (i === currentPage) {
                        addPaginationButton(i, i, true);
                    } else if (
                        i === 1 ||
                        i === totalPages ||
                        (i >= currentPage - 1 && i <= currentPage + 1)
                    ) {
                        addPaginationButton(i, i);
                    } else if (
                        i === currentPage - 2 ||
                        i === currentPage + 2
                    ) {
                        addPaginationButton('...', null, false, true);
                    }
                }

                // Next button
                if (currentPage < totalPages) {
                    addPaginationButton('›', currentPage + 1);
                }
            }

            function addPaginationButton(text, page, isActive = false, isDisabled = false) {
                const li = document.createElement('li');
                li.classList.add('page-item');
                if (isActive) li.classList.add('active');
                if (isDisabled) li.classList.add('disabled');

                const button = document.createElement('button');
                button.classList.add('page-link');
                button.textContent = text;

                if (!isDisabled && page) {
                    button.addEventListener('click', () => loadHistory(page));
                }

                li.appendChild(button);
                paginationContainer.appendChild(li);
            }

            submitBtn.addEventListener('click', function(e) {

                e.preventDefault();

                resultDiv.style.display = 'none';
                errorDiv.style.display = 'none';
                resultDiv.innerHTML = '';
                errorDiv.innerHTML = '';

                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        resultDiv.innerHTML = '<strong>Result:</strong> ' + data.result;
                        resultDiv.style.display = 'block';
                        form.reset();
                        setTimeout(() => resultDiv.style.display = 'none', 3000);

                        loadHistory(); // cập nhật lại lịch sử
                    })
                    .catch(error => {
                        if (error.errors) {
                            const list = Object.values(error.errors).map(e => `<li>${e[0]}</li>`).join(
                                '');
                            errorDiv.innerHTML = `<ul>${list}</ul>`;
                        } else if (error.error) {
                            errorDiv.innerHTML = `<p>${error.error}</p>`;
                        } else {
                            errorDiv.innerHTML = `<p>{{ __('Something went wrong.') }}</p>`;
                        }
                        errorDiv.style.display = 'block';
                    });
            });

            clearBtn.addEventListener('click', function() {
                if (!confirm("{{ __('Are you sure you want to clear the calculation history?') }}")) return;
                historyLoading.style.display = 'block';
                fetch("{{ route('frontend.auth.calculator.history.clear') }}", {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(() => {
                        loadHistory();
                    })
                    .catch(() => {
                        historyList.innerHTML =
                            "<li class='list-group-item text-danger'>{{ __('Failed to clear history.') }}</li>";
                    })
                    .finally(() => {
                        historyLoading.style.display = 'none';
                    });
            });

            loadHistory(1); // Load trang đầu tiên
        });
    </script>

</div>
