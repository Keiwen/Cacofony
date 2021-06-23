<?php

namespace Keiwen\Cacofony\ParamFetcher;


use Keiwen\Cacofony\ParamFetcher\Annotation\RequestParam;
use Symfony\Component\HttpFoundation\Request;
use Keiwen\Cacofony\Http\Request as CacoRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Keiwen\Cacofony\HttpException\ServerErrorHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints;

class ParamFetcher
{

    /** @var Request */
    protected $request;
    /** @var ParamReader */
    protected $paramReader;
    /** @var string */
    protected $method;
    /** @var ValidatorInterface */
    protected $validator;

    public function __construct(Request $request, ParamReader $paramReader, string $method, ValidatorInterface $validator = null)
    {
        $this->request = $request;
        $this->paramReader = $paramReader;
        $this->method = $method;
        $this->validator = $validator;
    }


    /**
     * @param string $name
     * @param string $mustMatch
     * @return mixed
     */
    public function get(string $name, string $mustMatch = '')
    {
        $parameters = $this->paramReader->read($this->method);
        if(empty($parameters[$name])) {
            throw new ServerErrorHttpException(sprintf("No annotation configured for parameter '%s'.", $name));
        }

        $parameter = $parameters[$name];
        $defaultValue = $parameter->getDefault();
        $value = $parameter->getValue($this->request, $defaultValue);

        $constraints = $parameter->getConstraints();
        if(!empty($mustMatch)) {
            $constraints[] = new Constraints\Regex(array(
                'pattern' => '#^(?:'.$mustMatch.')$#xsu',
                'message' => sprintf(
                    'Parameter \'%s\' value does not match specified format \'%s\'',
                    $parameter->getName(),
                    $mustMatch
                ),
            ));
        }
        if(!empty($constraints)) {
            $errors = $this->validator->validate($value, $constraints);
            if($errors->count() > 0) {
                $this->handleValidatorErrors($parameter, $errors);
            }
        }

        if($this->request instanceof CacoRequest) {
            $this->request->addRetrievedParameter($name, $value);
        }
        return $value;
    }


    /**
     * @param RequestParam                     $parameter
     * @param ConstraintViolationListInterface $errors
     * @throws BadRequestHttpException
     */
    protected function handleValidatorErrors(RequestParam $parameter, ConstraintViolationListInterface $errors)
    {
        $message = '';
        foreach($errors as $error) {
            $message .= sprintf(
                'Parameter "%s", value "%s" violated a constraint: "%s"' . PHP_EOL,
                $parameter->getName(),
                $error->getInvalidValue(),
                $error->getMessage()
            );
        }
        throw new BadRequestHttpException($message);
    }

}
