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
class Accepted extends Rule
{
    /** @var bool */
    protected $implicit = true;

    /** @var string */
    protected $message = "The :attribute must be accepted";

    /**
     * Check the $value is accepted
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $acceptables = ['yes', 'on', '1', 1, true, 'true'];
        return in_array($value, $acceptables, true);
    }
}
