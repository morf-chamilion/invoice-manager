<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary']) }} id="form_submit">
    <span id="children">
        {{ $slot }}
    </span>
    <span id="btn_loading" style="display: none;">
        Processing <span class="spinner-border spinner-border-sm ms-2"></span>
    </span>
</button>
<script>
    (function() {
        const resourceForm = document.querySelector('form#resource_form');

        function handleFormSubmit(event) {
            event.preventDefault();

            document.getElementById('btn_loading').style.display = 'inline-block';
            document.getElementById('children').style.display = 'none';
            document.getElementById('form_submit').style.cursor = 'wait';

            document.querySelectorAll("#resource_form_fieldset").forEach(function(element) {
                const blockUI = new KTBlockUI(element);
                if (blockUI.hasOwnProperty('block')) {
                    blockUI.block();
                }
            });

            setTimeout(() => {
                resourceForm.submit();
            }, 400);
        }

        if (resourceForm) {
            resourceForm.addEventListener('submit', handleFormSubmit);
        }
    })()
</script>
