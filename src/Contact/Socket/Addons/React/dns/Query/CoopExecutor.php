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
final class CoopExecutor implements ExecutorInterface
{
    private $executor;
    private $pending = array();
    private $counts = array();

    public function __construct(ExecutorInterface $base)
    {
        $this->executor = $base;
    }

    public function query(Query $query)
    {
        $key = $this->serializeQueryToIdentity($query);
        if (isset($this->pending[$key])) {
            // same query is already pending, so use shared reference to pending query
            $promise = $this->pending[$key];
            ++$this->counts[$key];
        } else {
            // no such query pending, so start new query and keep reference until it's fulfilled or rejected
            $promise = $this->executor->query($query);
            $this->pending[$key] = $promise;
            $this->counts[$key] = 1;

            $pending =& $this->pending;
            $counts =& $this->counts;
            $promise->then(function () use ($key, &$pending, &$counts) {
                unset($pending[$key], $counts[$key]);
            }, function () use ($key, &$pending, &$counts) {
                unset($pending[$key], $counts[$key]);
            });
        }

        // Return a child promise awaiting the pending query.
        // Cancelling this child promise should only cancel the pending query
        // when no other child promise is awaiting the same query.
        $pending =& $this->pending;
        $counts =& $this->counts;
        return new Promise(function ($resolve, $reject) use ($promise) {
            $promise->then($resolve, $reject);
        }, function () use (&$promise, $key, $query, &$pending, &$counts) {
            if (--$counts[$key] < 1) {
                unset($pending[$key], $counts[$key]);
                $promise->cancel();
                $promise = null;
            }
            throw new \RuntimeException('DNS query for ' . $query->name . ' has been cancelled');
        });
    }

    private function serializeQueryToIdentity(Query $query)
    {
        return sprintf('%s:%s:%s', $query->name, $query->type, $query->class);
    }
}
