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
use Kiaan\Contact\Socket\Addons\React\EventLoop\LoopInterface;
use Kiaan\Contact\Socket\Addons\React\Promise\Timer;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
final class TimeoutExecutor implements ExecutorInterface
{
    private $executor;
    private $loop;
    private $timeout;

    public function __construct(ExecutorInterface $executor, $timeout, LoopInterface $loop)
    {
        $this->executor = $executor;
        $this->loop = $loop;
        $this->timeout = $timeout;
    }

    public function query(Query $query)
    {
        return Timer\timeout($this->executor->query($query), $this->timeout, $this->loop)->then(null, function ($e) use ($query) {
            if ($e instanceof Timer\TimeoutException) {
                $e = new TimeoutException(sprintf("DNS query for %s timed out", $query->name), 0, $e);
            }
            throw $e;
        });
    }
}
