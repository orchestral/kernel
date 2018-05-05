<?php

namespace Orchestra\TestCase\Integration\Database;

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Auth\User;
use Orchestra\Database\SearchFilter;

class SearchFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../factories');
        $this->loadLaravelMigrations();
    }

    /** @test */
    public function it_can_search_by_exact_rules()
    {
        factory(User::class)->times(2)->create();
        factory(User::class)->times(3)->create([
            'remember_token' => null,
        ]);

        $search = new class('is:inactive') extends SearchFilter {
            protected function rules(): array
            {
                return [
                    'is:inactive' => function ($query) {
                        $query->whereNull('remember_token');
                    },
                    'email:*' => function ($query, $email) {
                        $query->where('email', '=', $email);
                    },
                ];
            }
        };

        $query = User::query();

        $search->handle($query);

        $this->assertSame(3, $query->count());
    }

    /** @test */
    public function it_can_search_by_wildcard_rules()
    {
        factory(User::class)->times(2)->create();
        $me = factory(User::class)->create([
            'name' => 'Mior Muhammad Zaki',
            'email' => 'crynobone@gmail.com',
        ]);

        $search = new class('email:crynobone@gmail.com') extends SearchFilter {
            protected function rules(): array
            {
                return [
                    'is:inactive' => function ($query) {
                        $query->whereNull('remember_token');
                    },
                    'email:*' => function ($query, $email) {
                        $query->where('email', '=', $email);
                    },
                ];
            }
        };

        $query = User::query();

        $search->handle($query);

        $this->assertSame(1, $query->count());

        $user = $query->first();

        $this->assertSame($me->name, $user->name);
        $this->assertTrue($me->is($user));
    }
}
