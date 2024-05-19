@if ($errors->any())
    <div class="alert alert-danger">
        <h4 class="mb-2">{{ $errors->first() }}</h4>
        <span>We've pinpointed the problems for you. Check the fields with error messages below.</span>
    </div>
@endif
