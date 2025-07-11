<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .panel {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th,
        .table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .table th {
            background-color: #f7fafc;
            font-weight: bold;
        }

        .result {
            font-weight: bold;
            color: #2d3748;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #48bb78;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #718096;
        }

        .small-text {
            font-size: 12px;
            color: #718096;
        }
    </style>
</head>

<body>
    <div class="header">
        Calculation Report Summary
    </div>

    <p>Dear {{ $user->name }},</p>

    <p>We've prepared a summary of calculations performed in the last 10 minutes.</p>

    <div class="panel">
        <h2>Calculation Details</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Operation</th>
                    <th>Details</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($calculations as $calc)
                    <tr>
                        <td>{{ $calc->operation }}</td>
                        <td>{{ $calc->number1 }} {{ $calc->operation}} {{ $calc->number2 }}</td>
                        <td class="result">{{ $calc->result }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <p>If you have any questions about these calculations, please don't hesitate to contact our support team.</p>

    <div class="footer">
        <p>Best regards,<br>
            {{ config('app.name') }}</p>

        <p class="small-text">This is an automated message. Please do not reply directly to this email.</p>
    </div>
</body>

</html>
