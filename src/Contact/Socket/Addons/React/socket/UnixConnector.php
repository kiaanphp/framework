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
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\Addons\React\EventLoop\LoopInterface;
use Kiaan\Contact\Socket\Addons\React\Promise;
use InvalidArgumentException;
use RuntimeException;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
final class UnixConnector implements ConnectorInterface
{
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function connect($path)
    {
        if (\strpos($path, '://') === false) {
            $path = 'unix://' . $path;
        } elseif (\substr($path, 0, 7) !== 'unix://') {
            return Promise\reject(new \InvalidArgumentException('Given URI "' . $path . '" is invalid'));
        }

        $resource = @\stream_socket_client($path, $errno, $errstr, 1.0);

        if (!$resource) {
            return Promise\reject(new \RuntimeException('Unable to connect to unix domain socket "' . $path . '": ' . $errstr, $errno));
        }

        $connection = new Connection($resource, $this->loop);
        $connection->unix = true;

        return Promise\resolve($connection);
    }
}
