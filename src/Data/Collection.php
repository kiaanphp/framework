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
namespace Kiaan\Data;

/*
|---------------------------------------------------
| Collection
|---------------------------------------------------
*/
class Collection {

    /**
    * Traits
    *
    */
    use Collection\ResponseTrait;
    use Collection\DataTrait;
    use Collection\ReturnsTrait;
    use Collection\MethodsTrait;
    use Collection\HelpersTrait;

    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    /*
    * List of data
    *
    */
    protected $list = array();
    
}