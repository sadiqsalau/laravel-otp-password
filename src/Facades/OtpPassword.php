<?php

namespace SadiqSalau\LaravelOtpPassword\Facades;

use SadiqSalau\LaravelOtpPassword\Contracts\OtpPasswordBroker as PasswordBroker;
use Illuminate\Support\Facades\Facade;

/**
 * OTP Password Facade
 *
 * @method static void createSession(string $email)
 * @method static string sendOtp(array $credentials)
 * @method static string reset(array $credentials, \Closure $callback)
 */
class OtpPassword extends Facade
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const OTP_SENT = PasswordBroker::OTP_SENT;

    /**
     * Constant representing a successfully reset password.
     *
     * @var string
     */
    const PASSWORD_RESET = PasswordBroker::PASSWORD_RESET;

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = PasswordBroker::INVALID_USER;

    /**
     * Constant representing an invalid session.
     *
     * @var string
     */
    const INVALID_SESSION = PasswordBroker::INVALID_SESSION;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth.password.otp';
    }
}
