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
namespace Kiaan\Security\Validator\Rules\Interfaces;

/*
|---------------------------------------------------
| Interface
|---------------------------------------------------
*/
interface ModifyValue
{
    /**
     * Modify given value
     * so in current and next rules returned value will be used
     *
     * @param mixed $value
     * @return mixed
     */
    public function modifyValue($value);
}
