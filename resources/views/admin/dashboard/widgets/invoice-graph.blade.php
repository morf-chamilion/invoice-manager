<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title fw-bold text-gray-800">
            {{ __('Revenue Over Time') }}
        </h3>
    </div>
    <div class="card-body d-flex justify-content-between flex-column px-2 pb-0 pt-5">
        <div class="mb-5 px-7 d-flex flex-stack">
            <div class="d-flex flex-wrap d-grid gap-5">
                <div class="me-md-2">
                    <div class="d-flex mb-2">
                        <span class="fs-2x fw-bold text-primary me-2 lh-1 ls-n2">
                            {{ $leadsAndContactsOverTime['leads']['total'] }}
                        </span>
                        <span class="fs-6 fw-semibold text-gray-500 align-self-end mb-1">
                            {{ __('Invoices') }}
                        </span>
                    </div>
                </div>
                <div class="me-md-2">
                    <div class="d-flex mb-2">
                        <span class="fs-2x fw-bold text-success me-2 lh-1 ls-n2">
                            {{ $leadsAndContactsOverTime['contacts']['total'] }}
                        </span>
                        <span class="fs-6 fw-semibold text-gray-500 align-self-end mb-1">
                            {{ __('Revenue') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-10">
                <span class="d-flex align-items-center">
                    <span class="bullet bullet-dot bg-primary h-8px w-8px me-2"></span>
                    <span class="fw-semibold text-gray-600">{{ __('lead.Plural') }}</span>
                </span>
                <span class="d-flex align-items-center">
                    <span class="bullet bullet-dot bg-success h-8px w-8px me-2"></span>
                    <span class="fw-semibold text-gray-600">{{ __('contact.Plural') }}</span>
                </span>
            </div>
        </div>
        <div id="kt_lead_chart" style="height: 350px;"></div>
    </div>
</div>

@push('footer')
    <script>
        function initDualLineChart(labels, leadsData, contactsData) {
            let element = document.getElementById('kt_lead_chart');

            let height = parseInt(KTUtil.css(element, 'height'));
            let labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
            let borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
            let primaryColor = KTUtil.getCssVariableValue('--bs-primary');
            let successColor = KTUtil.getCssVariableValue('--bs-success');
            let primaryLightColor = KTUtil.getCssVariableValue('--bs-primary-light');
            let successLightColor = KTUtil.getCssVariableValue('--bs-success-light');

            let options = {
                series: [{
                        name: 'Leads',
                        data: leadsData,
                    },
                    {
                        name: 'Contacts',
                        data: contactsData,
                    }
                ],
                chart: {
                    fontFamily: 'inherit',
                    type: 'area',
                    height: height,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {},
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false,
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.3,
                        gradientToColors: [primaryLightColor, successLightColor],
                        inverseColors: false,
                        opacityFrom: 0.8,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    show: true,
                    width: 3,
                    colors: [primaryColor, successColor]
                },
                xaxis: {
                    categories: labels,
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    },
                    crosshairs: {
                        position: 'front',
                        stroke: {
                            color: primaryColor,
                            width: 1,
                            dashArray: 3
                        }
                    },
                    tooltip: {
                        enabled: true,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    }
                },
                states: {
                    normal: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    hover: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: '12px'
                    },
                    y: {
                        formatter: function(value, {
                            seriesIndex
                        }) {
                            let label = seriesIndex === 0 ? 'leads' : 'contacts';
                            return Number(value).toFixed(0) + ' ' + label;
                        }
                    }
                },
                colors: [primaryColor, successColor],
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                markers: {
                    strokeWidth: 3,
                    strokeColors: [primaryColor, successColor],
                    fillColors: [primaryColor, successColor],
                    size: 4
                }
            };

            if (element) {
                let chart = new ApexCharts(element, options);
                chart.render();
            }
        }

        initDualLineChart(
            @json($leadsAndContactsOverTime['labels']),
            @json($leadsAndContactsOverTime['leads']['values']),
            @json($leadsAndContactsOverTime['contacts']['values'])
        );
    </script>
@endpush
