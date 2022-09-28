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
class Sometimes extends Rule
{
    protected $implicit = true;

    public function check($value): bool
    {
        if(is_null($value)){
            return true;
        }else{
            $this->implicit = false;

            return true;
        }
    }
    
}
