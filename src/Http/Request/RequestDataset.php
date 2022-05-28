<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
**/

/*
|---------------------------------------------------
| Namespaces
|---------------------------------------------------
*/
namespace Kiaan\Http\Request;

/*
|---------------------------------------------------
| Request Dataset
|---------------------------------------------------
*/
class RequestDataset
{
    /**
     * @var array
     * request's files array
     */
    public  $files = [];

    /**
     * @var array
     * request's params array
     */
    public $params = [];
}
