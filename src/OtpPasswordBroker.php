<?php

namespace SadiqSalau\LaravelOtpPassword;

use Closure;
use UnexpectedValueException;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use SadiqSalau\LaravelOtp\Facades\Otp;
use SadiqSalau\LaravelOtpPassword\Contracts\OtpPasswordBroker as PasswordBrokerContract;
use SadiqSalau\LaravelOtpPassword\Otp\PasswordResetOtp;

class OtpPasswordBroker implements PasswordBrokerContract
{
    /**
     * Password reset store key
     *
     * @var string
     */
    const STORE_KEY = 'otp_password_reset';

    /**
     * Initiate the broker
     *
     * @param \Illuminate\Contracts\Auth\UserProvider $users
     * @param int $expire
     */
    public function __construct(
        protected $users,
        protected $expire
    ) {
    }

    /**
     * Send OTP to user
     *
     * @param array $credentials
     * @return string
     */
    public function sendOtp($credentials)
    {
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        $email = $user->getEmailForPasswordReset();

        Otp::identifier($email)->send(
            new PasswordResetOtp($email),
            Notification::route('mail', $email)
        );

        return static::OTP_SENT;
    }

    /**
     * Reset the password.
     *
     * @param  array  $credentials
     * @param  \Closure  $callback
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback)
    {
        $user = $this->validateReset($credentials);

        if (!$user instanceof CanResetPassword) {
            return $user;
        }

        $password = $credentials['password'];

        $callback($user, $password);

        $this->clearSession($user);

        return static::PASSWORD_RESET;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|null
     *
     * @throws \UnexpectedValueException
     */
    protected function getUser($credentials)
    {
        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && !$user instanceof CanResetPassword) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (!$this->sessionExists($user)) {
            return static::INVALID_SESSION;
        }

        return $user;
    }

    /**
     * Create password reset session
     *
     * @param string $email
     * @return void
     */
    public function createSession($email)
    {
        // Mark this user as ready for password reset
        Cache::put(
            $this->getStorageKey($email),
            true,
            now()->addMinutes($this->expire)
        );
    }

    /**
     * Checks if password reset session exists and hasn't expired
     *
     * @param CanResetPassword $user
     * @return bool
     */
    protected function sessionExists($user)
    {
        return Cache::has(
            $this->getStorageKey(
                $user->getEmailForPasswordReset()
            )
        );
    }

    /**
     * Clear password reset session
     *
     * @param CanResetPassword $user
     * @return void
     */
    protected function clearSession($user)
    {
        Cache::forget(
            $this->getStorageKey(
                $user->getEmailForPasswordReset()
            )
        );
    }

    /**
     * Get the unique store key
     *
     * @param string $identifier
     * @return string
     */
    protected function getStorageKey($identifier)
    {
        return static::STORE_KEY . '_' . md5($identifier);
    }
}
