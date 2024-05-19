@extends('pdf.template')

@section('content')
    @include('pdf.partials.invoice-content', ['pdf' => true])
@endsection
