@extends('mail.template')

@section('body')
    <table style="padding: 20px;">
        <tbody>
            <tr>
                <td>
                    <p style="text-align:center;">
                        {{ __('Please click the link below to verify your account.') }}
                    </p>
                    <a href="{{ $verificationUrl }}" class="button button-primary">
                        {{ __('Verify Account') }}
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
@endsection()
