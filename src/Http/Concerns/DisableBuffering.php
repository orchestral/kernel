<?php

namespace Orchestra\Http\Concerns;

trait DisableBuffering
{
    /**
     * Disable output buffering.
     */
    protected function disableOutputBuffering(): void
    {
        // Turn off output buffering
        \ini_set('output_buffering', 'off');

        //Flush (send) the output buffer and turn off output buffering
        if (! \app()->environment('testing')) {
            while (@\ob_end_flush()) {
                //
            }
        }

        // Turn off PHP output compression
        \rescue(static function () {
            \ini_set('zlib.output_compression', false);
        }, null, false);

        // Implicitly flush the buffer(s)
        \ini_set('implicit_flush', true);
        \ob_implicit_flush(true);
    }
}
