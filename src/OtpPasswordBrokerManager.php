<?php

namespace SadiqSalau\LaravelOtpPassword;

use InvalidArgumentException;
use Illuminate\Auth\Passwords\PasswordBrokerManager as BrokerManager;
use SadiqSalau\LaravelOtpPassword\OtpPasswordBroker;

class OtpPasswordBrokerManager extends BrokerManager
{
    /**
     * Resolve the given broker.
     *
     * @param string|null $name
     * @throws InvalidArgumentException
     * @return OtpPasswordBroker
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }


        return new OtpPasswordBroker(
            $this->app['auth']->createUserProvider($config['provider'] ?? null),
            $config['expire']
        );
    }
}
