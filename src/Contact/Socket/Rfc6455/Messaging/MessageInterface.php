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
namespace Kiaan\Contact\Socket\RFC6455\Messaging;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
interface MessageInterface extends DataInterface, \Traversable, \Countable {
    /**
     * @param FrameInterface $fragment
     * @return MessageInterface
     */
    function addFrame(FrameInterface $fragment);

    /**
     * @return int
     */
    function getOpcode();

    /**
     * @return bool
     */
    function isBinary();
}
