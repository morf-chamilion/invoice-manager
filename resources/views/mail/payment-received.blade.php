@extends('mail.template')

@section('body')
    <table width="100%">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center; margin: 30px 0; padding: 20px 0; font-size: 18px;">
                    {{ __('Payment Received') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @if ($invoice->customer->name)
                <tr>
                    <th style="text-align: left;background-color: #eee;padding: 10px 14px;border: 1px solid #e3e3e3;">
                        {{ __('Billed To') }}
                    </th>
                    <td style="border: 1px solid #e3e3e3;padding: 10px 14px;">
                        {{ $invoice->customer->name }}
                    </td>
                </tr>
            @endif
            @if ($invoice->number)
                <tr>
                    <th width="40%"
                        style="text-align: left;background-color: #eee;padding: 10px 14px;border: 1px solid #e3e3e3;">
                        {{ __('Invoice Number') }}
                    </th>
                    <td style="border: 1px solid #e3e3e3;padding: 10px 14px;">
                        <a href="{{ route(InvoiceRoutePath::SHOW, $invoice) }}">
                            {{ $invoice->number }}
                        </a>
                    </td>
                </tr>
            @endif
            @if ($invoice->payment_data['transaction_id'])
                <tr>
                    <th width="40%"
                        style="text-align: left;background-color: #eee;padding: 10px 14px;border: 1px solid #e3e3e3;">
                        {{ __('Transaction ID') }}
                    </th>
                    <td style="border: 1px solid #e3e3e3;padding: 10px 14px;">
                        {{ $invoice->payment_data['transaction_id'] }}
                    </td>
                </tr>
            @endif
            @if ($invoice->payment_data['amount'])
                <tr>
                    <th style="text-align: left;background-color: #eee;padding: 10px 14px;border: 1px solid #e3e3e3;">
                        {{ __('Amount Paid') }}
                    </th>
                    <td style="border: 1px solid #e3e3e3;padding: 10px 14px;">
                        {{ MoneyHelper::print($invoice->payment_data['amount']) }}
                    </td>
                </tr>
            @endif
            @if ($invoice->payment_date)
                <tr>
                    <th style="text-align: left;background-color: #eee;padding: 10px 14px;border: 1px solid #e3e3e3;">
                        {{ __('Payment Date') }}
                    </th>
                    <td style="border: 1px solid #e3e3e3;padding: 10px 14px;">
                        {{ $invoice->payment_date }}
                    </td>
                </tr>
            @endif
            <tr>
                <th style="text-align: left;background-color: #eee;padding: 10px 14px;border: 1px solid #e3e3e3;">
                    {{ __('Payment Status') }}
                </th>
                <td style="border: 1px solid #e3e3e3;padding: 10px 14px;">
                    {!! InvoicePaymentStatus::toBadge($invoice->payment_status) !!}
                </td>
            </tr>
        </tbody>
    </table>
@endsection()
