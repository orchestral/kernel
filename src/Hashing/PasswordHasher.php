<?php

namespace Orchestra\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Hashing\AbstractHasher;
use RuntimeException;

class PasswordHasher extends AbstractHasher implements HasherContract
{
    /**
     * Fallback Hasher.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * Construct a new password hasher.
     */
    public function __construct(HasherContract $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Hash the given value.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function make($value, array $options = [])
    {
        return $this->hasher->make($value, $options);
    }

    /**
     * Get information about the given hashed value.
     *
     * @param  string  $hashedValue
     *
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
     *
     * @return bool
     */
    public function check($value, $hashedValue, array $options = [])
    {
        try {
            return $this->hasher->check($value, $hashedValue, $options);
        } catch (RuntimeException $e) {
            return parent::check($value, $hashedValue, $options);
        }
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     *
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return $this->hasher->needsRehash($hashedValue, $options);
    }
}
