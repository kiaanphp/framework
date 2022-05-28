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
namespace Kiaan\Contact\Socket\Addons\React\Socket;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class FixedUriConnector implements ConnectorInterface
{
    private $uri;
    private $connector;

    /**
     * @param string $uri
     * @param ConnectorInterface $connector
     */
    public function __construct($uri, ConnectorInterface $connector)
    {
        $this->uri = $uri;
        $this->connector = $connector;
    }

    public function connect($_)
    {
        return $this->connector->connect($this->uri);
    }
}
