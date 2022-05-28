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
namespace Kiaan\Contact\Socket\Addons\Psr7;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\Addons\Psr\MessageInterface;
use Kiaan\Contact\Socket\Addons\Psr\RequestInterface;
use Kiaan\Contact\Socket\Addons\Psr\StreamInterface;
use Kiaan\Contact\Socket\Addons\Psr\UriInterface;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Functions {

/**
 * Returns the string representation of an HTTP message.
 *
 * @param MessageInterface $message Message to convert to a string.
 *
 * @return string
 *
 * @deprecated str will be removed in guzzlehttp/psr7:2.0. Use Message::toString instead.
 */
public static function str(MessageInterface $message)
{
    return Message::toString($message);
}

/**
 * Parses a request message string into a request object.
 *
 * @param string $message Request message string.
 *
 * @return Request
 *
 * @deprecated parse_request will be removed in guzzlehttp/psr7:2.0. Use Message::parseRequest instead.
 */
public static function parse_request($message)
{
    return Message::parseRequest($message);
}

}
