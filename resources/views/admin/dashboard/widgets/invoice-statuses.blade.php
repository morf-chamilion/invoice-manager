<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title fw-bold text-gray-800">
            {{ __('Invoice Status Distribution') }}
        </h3>
    </div>
    <div class="card-body d-flex justify-content-between flex-column pb-0 px-2 pt-5">
        <div id="kt_lead_sources_donut_chart" style="height: 410px;"></div>
    </div>
</div>

@push('footer')
    <script>
        function initLeadSourcesDonutChart(data) {
            let element = document.getElementById('kt_lead_sources_donut_chart');

            let height = parseInt(KTUtil.css(element, 'height'));
            let labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
            let borderColor = KTUtil.getCssVariableValue('--bs-gray-200');

            const colors = [
                KTUtil.getCssVariableValue('--bs-primary'),
                KTUtil.getCssVariableValue('--bs-success'),
                KTUtil.getCssVariableValue('--bs-warning'),
                KTUtil.getCssVariableValue('--bs-info'),
                KTUtil.getCssVariableValue('--bs-danger'),
                KTUtil.getCssVariableValue('--bs-secondary'),
                KTUtil.getCssVariableValue('--bs-dark'),
            ];

            let options = {
                series: data.map(item => item.count),
                chart: {
                    fontFamily: 'inherit',
                    type: 'donut',
                    height: height,
                    toolbar: {
                        show: false
                    }
                },
                labels: data.map(item => item.source_name),
                colors: colors.slice(0, data.length),
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    color: labelColor,
                                    formatter: function(w) {
                                        return data.reduce((sum, item) => sum + item.count, 0);
                                    }
                                },
                                value: {
                                    show: true,
                                    fontSize: '14px',
                                    fontWeight: 500,
                                    color: labelColor,
                                    formatter: function(val) {
                                        return val;
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    },
                    formatter: function(val, opts) {
                        return Math.round(val) + '%';
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '12px',
                    markers: {
                        width: 8,
                        height: 8,
                        radius: 2
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 5
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
                            return value + ' leads';
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
                }
            };

            if (element) {
                let chart = new ApexCharts(element, options);
                chart.render();
            }
        }

        initLeadSourcesDonutChart(@json($leadSources->toArray()));
    </script>
@endpush
