<?php namespace Orchestra\Http;

use Illuminate\Foundation\Http\FormRequest as Request;
use Orchestra\Support\Traits\ValidationTrait;

class FormRequest extends Request
{
    use ValidationTrait;

    /**
     * Validate the form request according to its rules.
     *
     * @param  \Illuminate\Validation\Factory  $factory
     * @return void
     */
    public function validate(ValidationFactory $factory)
    {
        $this->setupValidationScenario();

        $resolver = $this->runValidation($this->input());

        if ($resolver->fails()) {
            throw new HttpResponseException($this->response(
                $this->formatErrors($instance)
            ));
        } elseif ($this->failsAuthorization()) {
            throw new HttpResponseException($this->forbiddenResponse());
        }

        $this->runFinalValidationChecks();
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

        if (in_array($method, $available)) {
            $this->onValidationScenario($available[$method]);
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
