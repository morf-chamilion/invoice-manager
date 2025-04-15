<div class="modal fade" tabindex="-1" id="payment_show">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    {{ __('Payment Details') }}
                </h3>

                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body">
                <div class="row" id="show_payment_data"></div>
            </div>

            <div class="modal-footer">
                <button type="reset" class="btn btn-light" data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('footer')
    <script>
        $(document).on('click', '.view-payment', function() {
            const $payment = $(this);

            const number = $payment.data('payment-number');
            const date = $payment.data('payment-date');
            const amount = $payment.data('payment-amount');
            const method = $payment.data('payment-method');
            const notes = $payment.data('payment-notes');
            const status = $payment.data('payment-status');
            const image = $payment.data('payment-image');

            const paymentData = $('#show_payment_data').html('');

            $('#payment_show_data').empty();

            if (number) {
                paymentData.append(
                    `<div class="mb-4 col-lg-4"><label class="col-form-label font-weight-bold">Reference Number</label><p>${number}</p></div>`
                );
            }

            if (amount) {
                paymentData.append(
                    `<div class="mb-4 col-lg-4"><label class="col-form-label font-weight-bold">Amount</label><p>${amount}</p></div>`
                );
            }

            if (date) {
                paymentData.append(
                    `<div class="mb-4 col-lg-4"><label class="col-form-label font-weight-bold">Date</label><p>${date}</p></div>`
                );
            }

            if (method) {
                paymentData.append(
                    `<div class="mb-4 col-lg-4"><label class="col-form-label font-weight-bold">Method</label><p>${method}</p></div>`
                );
            }

            if (status) {
                paymentData.append(
                    `<div class="mb-4 col-lg-4"><label class="col-form-label font-weight-bold">Status</label><p>${status}</p></div>`
                );
            }

            if (notes) {
                paymentData.append(
                    `<div class="mb-4 col-lg-4"><label class="col-form-label font-weight-bold">Notes</label><p>${notes}</p></div>`
                );
            }

            if (image) {
                paymentData.append(
                    `<div class="mb-4 col-lg-4"><label class="col-form-label font-weight-bold">Reference Receipt</label><a href="${image}" class="btn btn-light w-50 d-block" download><i class="fas fa-file-download me-2"></i>Download</a></div>`
                );
            }
        });
    </script>
@endpush
