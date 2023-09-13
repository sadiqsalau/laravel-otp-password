# Laravel OTP Password

## Introduction

This package makes it a breeze implementing password reset using the `[sadiqsalau/laravel-otp](https://github.com/sadiqsalau/laravel-otp)` package.

## Installation

Install via composer: This package depends on `sadiqsalau/laravel-otp`, you need to install the both of them.

```bash
composer require sadiqsalau/laravel-otp sadiqsalau/laravel-otp-password
```

## Usage

To use this package, you must have implemented your OTP verification system (https://github.com/sadiqsalau/laravel-otp#usage).

### Sending Reset OTP

Sending password reset OTP is similar to sending password reset link (https://laravel.com/docs/passwords).

```php
use Illuminate\Http\Request;
use SadiqSalau\LaravelOtpPassword\Facades\OtpPassword;

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = OtpPassword::sendOTP(
        $request->only('email')
    );

    return $status === OtpPassword::OTP_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');
```

Once the OTP has been sent, the user is expected to verify their OTP code, upon verification a password reset session would be created. This session will last for the number of minutes configured in `config/auth.php`.

```php
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'expire' => 15
        ],
    ],
```

### Resetting Password

```php
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use SadiqSalau\LaravelOtpPassword\Facades\OtpPassword;

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $status = OtpPassword::reset(
        $request->only(
            'email',
            'password',
            'password_confirmation'
        ),
        function ($user) use ($request) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status === OtpPassword::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');
```

## API

-   `OtpPassword::sendOtp(array $credentials)` - Send password reset OTP

-   `OtpPassword::reset(array $credentials, Closure $callback)` - Attempt to reset the password.

## Contribution

Contributions are welcomed.
