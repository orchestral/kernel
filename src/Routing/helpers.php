<?php

use Illuminate\Http\RedirectResponse;

if (! function_exists('redirect_with_errors')) {
    /**
     * Redirect with input and errors.
     *
     * @param  string  $to
     * @param  \Illuminate\Contracts\Support\MessageBag|array  $errors
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    function redirect_with_errors(string $to, $errors): RedirectResponse
    {
        return redirect($to)->withInput()->withErrors($errors);
    }
}

if (! function_exists('redirect_with_message')) {
    /**
     * Queue notification and redirect.
     *
     * @param  string  $to
     * @param  string|null  $message
     * @param  string  $type
     *
     * @return mixed
     */
    function redirect_with_message(
        string $to,
        ?string $message = null,
        string $type = 'success'
    ): RedirectResponse {
        ! is_null($message) && app('orchestra.messages')->add($type, $message);

        return redirect($to);
    }
}
