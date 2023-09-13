<?php

namespace SadiqSalau\LaravelOtpPassword\Otp;

use SadiqSalau\LaravelOtp\Contracts\OtpInterface as Otp;
use SadiqSalau\LaravelOtpPassword\Facades\OtpPassword;

class PasswordResetOtp implements Otp
{

    /**
     * Initiates the OTP
     *
     * @param string $email Email address
     */
    public function __construct(
        public string $email,
    ) {
    }

    /**
     * Create password reset session for the email address
     *
     * @return void
     */
    public function process()
    {
        OtpPassword::createSession($this->email);

        return ['email' => $this->email];
    }
}
