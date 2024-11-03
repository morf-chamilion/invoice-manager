@extends('pdf.template')

@section('content')
    @include('pdf.partials.quotation-content', ['pdf' => true])
@endsection
