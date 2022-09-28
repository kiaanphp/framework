<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Security\Validator\Rules;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Security\Validator\Rule;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Regex extends Rule
{

    /** @var string */
    protected $message = "The :attribute is not valid format";

    /** @var array */
    protected $fillableParams = ['regex'];

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);
        $regex = $this->parameter('regex');
        return preg_match($regex, $value) > 0;
    }
}
