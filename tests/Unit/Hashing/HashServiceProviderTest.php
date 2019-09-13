<?php

namespace Orchestra\TestCase\Unit\Hashing;

use Orchestra\Hashing\HashServiceProvider;
use PHPUnit\Framework\TestCase;

class HashServiceProviderTest extends TestCase
{
    /** @test */
    public function it_provides_hash_password()
    {
        $this->assertContains('hash.password', (new HashServiceProvider(null))->provides());
    }
}
