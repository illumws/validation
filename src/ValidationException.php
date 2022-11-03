<?php

namespace Illum\Validation;

use Exception;

class ValidationException extends Exception
{
    /**
     * The validator instance.
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    public $validator;

    /**
     * The status code to use for the response.
     *
     * @var int
     */
    public $status = 422;

    /**
     * The name of the error bag.
     *
     * @var string
     */
    public $errorBag;

    /**
     * The path the client should be redirected to.
     *
     * @var string
     */
    public $redirectTo;

    /**
     * Create a new exception instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @param  \Symfony\Component\HttpFoundation\Response|null  $response
     * @param  string  $errorBag
     * @return void
     */
    public function __construct($validator, $errorBag = 'default')
    {
        parent::__construct('The given data was invalid.');

        $this->errorBag = $errorBag;
        $this->validator = $validator;
    }

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function errors()
    {
        return $this->validator->errors()->messages();
    }

    /**
     * Set the HTTP status code to be used for the response.
     *
     * @param  int  $status
     * @return $this
     */
    public function status($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set the error bag on the exception.
     *
     * @param  string  $errorBag
     * @return $this
     */
    public function errorBag($errorBag)
    {
        $this->errorBag = $errorBag;

        return $this;
    }

    /**
     * Set the URL to redirect to on a validation error.
     *
     * @param  string  $url
     * @return $this
     */
    public function redirectTo($url)
    {
        $this->redirectTo = $url;

        return $this;
    }
}
