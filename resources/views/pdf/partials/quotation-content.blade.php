<table style="margin: 0; width: 100%; border-collapse: collapse;">
    <header style="width: 100%;">
        <table style="width: 100%; border-collapse: collapse; margin: 20px auto; font-family: sans-serif;">
            <tbody>
                <tr
                    style="vertical-align: top; @empty($pdf) display: flex; justify-content: space-between; @endempty">
                    <td colspan="2">
                        <h4 style="font-size: 32px; font-weight: bold; text-transform: uppercase; margin: 0;">
                            {{ __('Quotation') }}
                        </h4>
                        <span style="display: block; margin-top: 4px; font-size: 16px; font-weight: normal;">
                            {{ $quotation->number }}
                        </span>
                    </td>
                    <td colspan="4" style="text-align: right;">
                        @if ($logo = $quotation->vendor->getFirstMedia('logo'))
                            <img src="{{ isset($pdf) && $pdf ? $logo?->getPath() : $logo?->getFullUrl() }}"
                                alt="Company Logo" style="max-width: 150px;" />
                        @endif
                        @if ($content = settings(SettingModule::QUOTATION)->get('company_content'))
                            <div style="margin-top: 20px; font-weight: 400; font-size: 14px; text-align: right;">
                                {!! $content !!}
                            </div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </header>

    <table
        style="width: 100%; border-collapse: collapse; margin: 0 auto 20px; font-weight: 600; font-family: sans-serif">
        <tbody>
            <tr>
                <td colspan="2" style="font-size: 18px;">
                    {{ $quotation->customer->name }}
                </td>
            </tr>
            @if (optional($quotation->customer)->company)
                <tr>
                    <td colspan="2" style="font-size: 16px; font-weight: 400;">
                        {{ $quotation->customer->company }}
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="2">
                    <a style="font-size: 16px; font-weight: 400; margin: 0px;">{{ $quotation->customer->email }}</a>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p style="margin-top: 2px; font-size: 14px; font-weight: 400;">{!! nl2br($quotation->customer->address) !!}</p>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="width: 60%; border-collapse: collapse; margin-bottom: 35px; font-family: sans-serif">
        <thead>
            <tr>
                <td>
                    <span style="font-weight: bold; font-size: 13px; text-transform: uppercase; color: #817c7a">
                        {{ __('Date') }}
                    </span>
                </td>
                <td>
                    <span style="font-weight: bold; font-size: 13px; text-transform: uppercase; color: #817c7a">
                        {{ __('Due Date') }}
                    </span>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span style="font-weight: 400;">
                        {{ $quotation->readableDate }}
                    </span>
                </td>
                <td>
                    <span style="font-weight: 400;">
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="table-wrapper">
        <table style="width: 100%; border-collapse: collapse; margin: 0 auto 6px; font-family: sans-serif">
            <thead>
                <tr>
                    <th align="left" style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3;">
                        <p
                            style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                            {{ __('Item') }}
                        </p>
                    </th>
                    <th align="left" style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3;">
                        <p
                            style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                            {{ __('Description') }}
                        </p>
                    </th>
                    <th align="right"
                        style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; color: #555250; text-align: end;  @empty($pdf) width: 150px; @else width: 100px; @endempty">
                        <p style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                            {{ __('Unit Price (:currency)', ['currency' => $quotation->vendor->currency]) }}
                        </p>
                    </th>
                    <th align="right"
                        style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; color: #555250; text-align: end; @empty($pdf) width: 60px; @else width: 40px; @endempty">
                        <p style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                            {{ __('Qty') }}
                        </p>
                    </th>
                    <th align="right"
                        style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; color: #555250; text-align: end; @empty($pdf) width: 120px; @else width: 100px; @endempty">
                        <p style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                            {{ __('Amount (:currency)', ['currency' => $quotation->vendor->currency]) }}
                        </p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quotation->formattedQuotationItems as $quotationItem)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <p style="margin: 3px 0 0; font-size: 12px;">
                                {{ $quotationItem->title }}
                            </p>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <p style="margin: 3px 0 0; font-size: 12px;">
                                {{ $quotationItem->description }}
                            </p>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            <span style="font-size: 12px;">{{ MoneyHelper::format($quotationItem->unit_price) }}</span>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            <span style="font-size: 12px;">{{ $quotationItem->quantity }}</span>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                            <span style="font-size: 12px;">{{ MoneyHelper::format($quotationItem->amount) }}</span>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2"
                        style="border: 1px solid #ddd; padding: 8px; font-size: 18px; font-weight: bold;">
                        {{ __('Total Amount') }}
                    </td>
                    <td colspan="3"
                        style="border: 1px solid #ddd; padding: 8px; text-align: right; font-size: 18px; font-weight: bold;">
                        {{ $quotation->readableTotalPrice }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if ($quotation->notes)
        <table style="font-family: sans-serif">
            <tbody>
                <tr>
                    <td style="padding-top:
                    25px;">
                        <h6
                            style="margin-bottom: 0; font-weight: bold; font-size: 13px; text-transform: uppercase; color: #817c7a">
                            {{ __('Notes') }}
                        </h6>
                        <p style="margin-top: 6px; ">{{ $quotation->notes }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <footer
        style="@empty($pdf) margin-top: 30px; @else position: absolute; bottom: 0;  @endif width: 100%; font-size: 14px; font-family: sans-serif">
        {!! $quotation->vendor->quotation_footer_content !!}
    </footer>
</table>
