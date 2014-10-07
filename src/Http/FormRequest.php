<?php namespace Orchestra\Http;

use Orchestra\Support\Traits\ValidationTrait;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Factory as ValidationFactory;

class FormRequest extends Request
{
    use ValidationTrait;

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->setupValidationScenario();

        return $this->runValidation($this->input());
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
     * Get validation rules.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->container->call([$this, 'rules']);
    }
}
