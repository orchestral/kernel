<?php

namespace Orchestra\TestCase\Unit\Http\Transformer;

use Carbon\Carbon;
use Orchestra\Http\Transformer\InteractsWithDateTime;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class InteractsWithDateTimeTest extends TestCase
{
    use InteractsWithDateTime;

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_convert_to_date_string()
    {
        $this->assertSame('2015-04-01', $this->toDateString(Carbon::createFromDate(2015, 4, 1, 'UTC')->timezone('Asia/Kuala_Lumpur')));
        $this->assertNull($this->toDateString(null));
    }

    /** @test */
    public function it_can_convert_to_datetime_string()
    {
        $this->assertSame(
            '2015-04-01 04:45:10', $this->toDatetimeString(Carbon::create(2015, 4, 1, 12, 45, 10, 'Asia/Kuala_Lumpur'))
        );

        $this->assertStringContainsString(
            '2015-04-01', $this->toDatetimeString(Carbon::createFromDate(2015, 4, 1, 'UTC')->timezone('Asia/Kuala_Lumpur'))
        );

        $this->assertNull($this->toDateString(null));
    }

    /**
     * Get request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest()
    {
        $request = m::mock('Illuminate\Http\Request');

        $request->shouldReceive('header')->with('time-zone')->andReturn('UTC');

        return $request;
    }
}
