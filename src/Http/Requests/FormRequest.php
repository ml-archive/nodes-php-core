<?php

namespace Nodes\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as IlluminateFormRequest;
use Illuminate\Http\JsonResponse;
use Nodes\Validation\Exceptions\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class Request.
 */
class FormRequest extends IlluminateFormRequest
{
    /**
     * String with the fully qualified class name for
     * Dingo API Request class. This is to avoid
     * having Dingo as a dependency of the
     * core package.
     *
     * @var string
     */
    private $dingoRequestClass = 'Dingo\Api\Http\Request';


    /**
     * @var array
     */
    protected $errorCodes = [];

    /**
     * Retrieve errorCodes.
     *
     * @author Pedro Coutinho <peco@nodesagency.com>
     *
     * @return array
     */
    public function getErrorCodes()
    {
        return $this->errorCodes;
    }

    /**
     * Set errorCodes.
     *
     * @author Pedro Coutinho <peco@nodesagency.com>
     *
     * @param array $errorCodes
     *
     * @return FormRequest
     */
    public function setErrorCodes(array $errorCodes)
    {
        $this->errorCodes = $errorCodes;

        return $this;
    }

    /**
     * failedValidation.
     *
     * @author Pedro Coutinho <peco@nodesagency.com>
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @throws \Nodes\Validation\Exceptions\ValidationException
     *
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->isApiRequest()) {
            throw new ValidationException($validator, $this->getErrorCodes());
        }

        parent::failedValidation($validator);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return mixed
     */
    protected function failedAuthorization()
    {
        if ($this->isApiRequest()) {
            throw new HttpException(403);
        }

        //parent::failedAuthorization();
        abort(403);
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @author Pedro Coutinho <peco@nodesagency.com>
     *
     * @param array $errors
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function response(array $errors)
    {
        if (($this->ajax() && !$this->pjax()) || $this->wantsJson()) {
            return new JsonResponse($errors, ValidationException::VALIDATION_FAILED);
        }

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            // This makes errors display properly (original errors are placed under the key 'errors' instead of 'error')
            ->with('error', $this->getValidatorInstance()->getMessageBag());
    }

    /**
     * Checks if we are facing an API request (Dingo)
     * or a Web request.
     *
     * @author Pedro Coutinho <peco@nodesagency.com>
     *
     * @return bool
     */
    protected function isApiRequest()
    {
        return $this->container['request'] instanceof $this->dingoRequestClass;
    }
}
