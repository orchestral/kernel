<?php

namespace Orchestra\Hashing;

use RuntimeException;
use Illuminate\Contracts\Hashing\Hasher;

class Password
{
    /**
     * Construct a new password hasher.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher  $hasher
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Get information about the given hashed value.
     *
     * @param  string  $hashedValue
     * @return array
     */
    public function info($hashedValue)
    {
        return $this->hasher->info($hashedValue);
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = [])
    {
        try {
            return $this->hasher->check($value, $hashedValue, $options);
        } catch (RuntimeException $e) {
            //
        }

        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }
}
