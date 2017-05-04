<?php

namespace Keiwen\Cacofony\HttpException;

use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginTimeoutHttpException extends HttpException
{

    const HTTP_CODE = 440;

    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param int        $code     The internal exception code
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(static::HTTP_CODE, $message, $previous, array(), $code);
    }

}
