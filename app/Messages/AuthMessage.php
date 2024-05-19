<?php

namespace App\Messages;

class AuthMessage
{
	/**
	 * Invalid credentials message.
	 */
	public function invalidCredentials(): string
	{
		return 'The provided credentials are incorrect.';
	}

	/**
	 * Revoke access token failed message.
	 */
	public function revokeAccessTokenFailed(): string
	{
		return 'Failed to revoke access token.';
	}

	/**
	 * Get profile data failed message.
	 */
	public function getProfileFailed(): string
	{
		return 'Failed to get profile data';
	}

	/**
	 * Mail verification notice message.
	 */
	public function mailVerifyNotice(): string
	{
		return 'Check your email for verification instructions.';
	}

	/**
	 * Must verify mail message.
	 */
	public function mustVerifyMail(): string
	{
		return 'Email must be verified before login.';
	}

	/**
	 * Mail already verified message.
	 */
	public function mailAlreadyVerified(): string
	{
		return 'Email already verified.';
	}

	/**
	 * Mail verification failed message.
	 */
	public function mailVerifyFailed(): string
	{
		return 'Error caught while verifying your account though email.';
	}

	/**
	 * Mail verification success message.
	 */
	public function mailVerifySuccess(): string
	{
		return 'Successfully verified your account through email.';
	}
}
