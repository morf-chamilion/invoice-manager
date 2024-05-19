@extends('mail.template')

@section('body')
    <table style="padding: 20px;">
        <tbody>
            <tr>
                <td>
                    {!! $body !!}
                </td>
            </tr>
        </tbody>
    </table>
@endsection
