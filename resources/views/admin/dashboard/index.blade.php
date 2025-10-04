<x-default-layout>

    @if ($isSuperAdmin)
        <div class="row g-xl-10">
            <div class="col-lg-3 mb-8">
                @include('admin/dashboard/widgets/welcome')
            </div>

            <div class="col-lg-2 mb-8">
                @include('admin/dashboard/widgets/users', ['users' => $users])
            </div>

            <div class="col-lg-4 mb-8">
                @include('admin/dashboard/widgets/time')
            </div>

            <div class="col-lg-3 mb-8">
                @include('admin/dashboard/widgets/application')
            </div>
        </div>
    @else
        <div class="row g-xl-10">
            <div class="col-lg-3 mb-8">
                @include('admin/dashboard/widgets/total-amount-due', ['totalAmountDue' => $totalAmountDue])
            </div>

            <div class="col-lg-3 mb-8">
                @include('admin/dashboard/widgets/total-revenue-collected', [
                    'totalRevenueCollected' => $totalRevenueCollected,
                ])
            </div>

            <div class="col-lg-3 mb-8">
                @include('admin/dashboard/widgets/invoices-this-month', [
                    'invoicesThisMonth' => $invoicesThisMonth,
                ])
            </div>

            <div class="col-lg-3 mb-8">
                @include('admin/dashboard/widgets/conversion-rate', ['conversionRate' => $conversionRate])
            </div>
        </div>

        <div class="row g-xl-10">
            <div class="col-lg-12 col-xl-9 mb-8">
                @include('admin/dashboard/widgets/revenue-chart', [
                    'revenueChartData' => $revenueChartData,
                ])
            </div>

            <div class="col-lg-12 col-xl-3 mb-8">
                @include('admin/dashboard/widgets/invoice-status-donut', [
                    'invoiceStatusDistribution' => $invoiceStatusDistribution,
                ])
            </div>
        </div>
    @endif

</x-default-layout>
