<?php

namespace Orchestra\Http\Transformer;

use Carbon\Carbon;

trait InteractsWithDateTime
{
    /**
     * The request timezone code cache.
     *
     * @var null
     */
    protected static $requestHeaderTimezoneCode = null;

    /**
     * Get timezone from request header.
     */
    protected function getTimezoneFromRequestHeader(): string
    {
        if (\is_null(static::$requestHeaderTimezoneCode)) {
            static::$requestHeaderTimezoneCode = \optional($this->getRequest())->header('time-zone') ?? 'UTC';
        }

        return static::$requestHeaderTimezoneCode;
    }

    /**
     * Convert Carbon to datetime string or return null.
     *
     * @return string|null
     */
    protected function toDateString(Carbon $datetime = null)
    {
        return $datetime instanceof Carbon
                    ? $datetime->timezone($this->getTimezoneFromRequestHeader())->toDateString()
                    : null;
    }

    /**
     * Convert Carbon to datetime string or return null.
     *
     * @return string|null
     */
    protected function toDatetimeString(Carbon $datetime = null)
    {
        return $datetime instanceof Carbon
                    ? $datetime->timezone($this->getTimezoneFromRequestHeader())->toDatetimeString()
                    : null;
    }
}
