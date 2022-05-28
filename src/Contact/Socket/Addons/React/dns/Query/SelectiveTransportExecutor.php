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
namespace Kiaan\Contact\Socket\Addons\React\Dns\Query;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\Addons\React\Promise\Promise;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class SelectiveTransportExecutor implements ExecutorInterface
{
    private $datagramExecutor;
    private $streamExecutor;

    public function __construct(ExecutorInterface $datagramExecutor, ExecutorInterface $streamExecutor)
    {
        $this->datagramExecutor = $datagramExecutor;
        $this->streamExecutor = $streamExecutor;
    }

    public function query(Query $query)
    {
        $stream = $this->streamExecutor;
        $pending = $this->datagramExecutor->query($query);

        return new Promise(function ($resolve, $reject) use (&$pending, $stream, $query) {
            $pending->then(
                $resolve,
                function ($e) use (&$pending, $stream, $query, $resolve, $reject) {
                    if ($e->getCode() === (\defined('SOCKET_EMSGSIZE') ? \SOCKET_EMSGSIZE : 90)) {
                        $pending = $stream->query($query)->then($resolve, $reject);
                    } else {
                        $reject($e);
                    }
                }
            );
        }, function () use (&$pending) {
            $pending->cancel();
            $pending = null;
        });
    }
}
