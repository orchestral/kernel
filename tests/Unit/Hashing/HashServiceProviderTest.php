<?php

namespace Orchestra\TestCase\Unit\Hashing;

use PHPUnit\Framework\TestCase;
use Orchestra\Hashing\HashServiceProvider;

class HashServiceProviderTest extends TestCase
{
    /** @test */
    public function it_provides_hash_password()
    {
        $this->assertContains('hash.password', (new HashServiceProvider(null))->provides());
    }

}
