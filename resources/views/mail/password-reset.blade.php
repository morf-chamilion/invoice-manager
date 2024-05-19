@extends('mail.template')

@section('body')
    <table style="padding: 20px;">
        <tbody>
            <tr>
                <td align="center">
                    <p style="text-align:center;">
                        {{ __('You are receiving this email because we received a password reset request for your account.') }}
                    </p>
                    <a href="{{ $passwordResetUrl }}" class="button button-primary"
                        style="margin-bottom: 15px; text-align: center;">
                        {{ __('Reset Password') }}
                    </a>
                    <p style="text-align: center;">
                        {{ __('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]) }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
@endsection()
