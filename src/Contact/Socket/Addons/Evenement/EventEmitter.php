<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/
declare(strict_types=1);

/*
|---------------------------------------------------
| Namespaces
|---------------------------------------------------
*/ 
namespace Kiaan\Contact\Socket\Addons\Evenement;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class EventEmitter implements EventEmitterInterface
{
    use EventEmitterTrait;
}
