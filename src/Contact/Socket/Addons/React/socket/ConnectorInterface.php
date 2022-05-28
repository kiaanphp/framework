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
interface ConnectorInterface
{
    /**
     * Creates a streaming connection to the given remote address
     *
     * If returns a Promise which either fulfills with a stream implementing
     * `ConnectionInterface` on success or rejects with an `Exception` if the
     * connection is not successful.
     *
     * ```php
     * $connector->connect('google.com:443')->then(
     *     function (React\Socket\ConnectionInterface $connection) {
     *         // connection successfully established
     *     },
     *     function (Exception $error) {
     *         // failed to connect due to $error
     *     }
     * );
     * ```
     *
     * The returned Promise MUST be implemented in such a way that it can be
     * cancelled when it is still pending. Cancelling a pending promise MUST
     * reject its value with an Exception. It SHOULD clean up any underlying
     * resources and references as applicable.
     *
     * ```php
     * $promise = $connector->connect($uri);
     *
     * $promise->cancel();
     * ```
     *
     * @param string $uri
     * @return \React\Promise\PromiseInterface resolves with a stream implementing ConnectionInterface on success or rejects with an Exception on error
     * @see ConnectionInterface
     */
    public function connect($uri);
}
