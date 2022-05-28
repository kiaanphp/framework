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
namespace Kiaan\Contact\Socket\Addons\React\Promise;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
interface PromisorInterface
{
    /**
     * Returns the promise of the deferred.
     *
     * @return PromiseInterface
     */
    public function promise();
}
