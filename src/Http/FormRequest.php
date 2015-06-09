<?php namespace Orchestra\Http;

use Orchestra\Support\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Validation\Factory as ValidationContract;

class FormRequest extends Request
{
    use ValidationTrait;

    /**
     * Get validation rules.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->container->call([$this, 'rules']);
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->setupValidationScenario();
        $this->setupValidationParameters();

        $this->validationFactory    = $this->container->make(ValidationContract::class);
        $this->validationDispatcher = $this->container->make(DispatcherContract::class);

        return $this->runValidation($this->all(), [], $this->messages());
    }

    /**
     * Setup validation scenario based on request method.
     *
     * @return void
     */
    protected function setupValidationScenario()
    {
        $current   = $this->method();
        $available = [
            'POST'   => 'store',
            'PUT'    => 'update',
            'DELETE' => 'destroy',
        ];

        if (in_array($current, $available)) {
            $this->onValidationScenario($available[$current]);
        }
    }

    /**
     * Setup validation scenario based on request method.
     *
     * @return void
     */
    protected function setupValidationParameters()
    {
        $parameters = $this->route()->parametersWithoutNulls();

        $this->bindToValidation($parameters);
    }
}
