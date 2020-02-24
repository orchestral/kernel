<?php

use Illuminate\Http\RedirectResponse;

if (! \function_exists('redirect_with_errors')) {
    /**
     * Redirect with input and errors.
     *
     * @param  \Illuminate\Contracts\Support\MessageBag|array  $errors
     */
    function redirect_with_errors(string $to, $errors): RedirectResponse
    {
        return \redirect($to)->withInput()->withErrors($errors);
    }
}

if (! \function_exists('redirect_with_message')) {
    /**
     * Queue notification and redirect.
     *
     * @return mixed
     */
    function redirect_with_message(
        string $to,
        ?string $message = null,
        string $type = 'success'
    ): RedirectResponse {
        $bag = \app('orchestra.messages');

        if (! empty($message)) {
            $bag->add($type, $message);
        }

        return \redirect($to)->with('message', $bag->getMessages());
    }
}
