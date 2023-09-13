<?php

namespace SadiqSalau\LaravelOtpPassword\Contracts;

use Closure;

interface OtpPasswordBroker
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const OTP_SENT = 'passwords.otp';

    /**
     * Constant representing a successfully reset password.
     *
     * @var string
     */
    const PASSWORD_RESET = 'passwords.reset';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'passwords.user';

    /**
     * Constant representing an invalid session.
     *
     * @var string
     */
    const INVALID_SESSION = 'passwords.session';

    /**
     * Sends a password reset OTP.
     *
     * @param  array  $credentials
     * @return string
     */
    public function sendOtp(array $credentials);

    /**
     * Reset the password.
     *
     * @param  array  $credentials
     * @param  \Closure  $callback
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback);
}
