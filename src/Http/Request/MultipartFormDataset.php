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
| Multi part Form Dataset
|---------------------------------------------------
*/
class MultipartFormDataset
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
