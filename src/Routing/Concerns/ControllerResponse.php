<?php

namespace Orchestra\Routing\Concerns;

use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ControllerResponse
{
    /**
     * Queue notification and redirect.
     *
     * @param  string  $to
     * @param  string|null  $message
     * @param  string  $type
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectWithMessage(
        string $to,
        ?string $message = null,
        string $type = 'success'
    ): RedirectResponse {
        return redirect_with_message($to, $message, $type);
    }

    /**
     * Redirect with input and errors.
     *
     * @param  string  $to
     * @param  \Illuminate\Contracts\Support\MessageBag|array  $errors
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectWithErrors(string $to, $errors)
    {
        return redirect_with_errors($to, $errors);
    }

    /**
     * Redirect.
     *
     * @param  string  $to
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(string $to): RedirectResponse
    {
        return redirect($to);
    }

    /**
     * Halt current request using App::abort().
     *
     * @param  int  $code
     * @param  string  $message
     * @param  array  $headers
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return void
     */
    public function suspend(int $code, string $message = '', array $headers = []): void
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message, null, $headers);
    }
}
