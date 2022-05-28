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
namespace Kiaan\Contact\Socket\Addons\React\Dns\Resolver;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\Addons\React\Cache\ArrayCache;
use Kiaan\Contact\Socket\Addons\React\Cache\CacheInterface;
use Kiaan\Contact\Socket\Addons\React\Dns\Config\HostsFile;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\CachingExecutor;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\CoopExecutor;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\ExecutorInterface;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\HostsFileExecutor;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\RetryExecutor;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\SelectiveTransportExecutor;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\TcpTransportExecutor;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\TimeoutExecutor;
use Kiaan\Contact\Socket\Addons\React\Dns\Query\UdpTransportExecutor;
use Kiaan\Contact\Socket\Addons\React\EventLoop\LoopInterface;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
final class Factory
{
    /**
     * @param string        $nameserver
     * @param LoopInterface $loop
     * @return \React\Dns\Resolver\ResolverInterface
     */
    public function create($nameserver, LoopInterface $loop)
    {
        $executor = $this->decorateHostsFileExecutor($this->createExecutor($nameserver, $loop));

        return new Resolver($executor);
    }

    /**
     * @param string          $nameserver
     * @param LoopInterface   $loop
     * @param ?CacheInterface $cache
     * @return \React\Dns\Resolver\ResolverInterface
     */
    public function createCached($nameserver, LoopInterface $loop, CacheInterface $cache = null)
    {
        // default to keeping maximum of 256 responses in cache unless explicitly given
        if (!($cache instanceof CacheInterface)) {
            $cache = new ArrayCache(256);
        }

        $executor = $this->createExecutor($nameserver, $loop);
        $executor = new CachingExecutor($executor, $cache);
        $executor = $this->decorateHostsFileExecutor($executor);

        return new Resolver($executor);
    }

    /**
     * Tries to load the hosts file and decorates the given executor on success
     *
     * @param ExecutorInterface $executor
     * @return ExecutorInterface
     * @codeCoverageIgnore
     */
    private function decorateHostsFileExecutor(ExecutorInterface $executor)
    {
        try {
            $executor = new HostsFileExecutor(
                HostsFile::loadFromPathBlocking(),
                $executor
            );
        } catch (\RuntimeException $e) {
            // ignore this file if it can not be loaded
        }

        // Windows does not store localhost in hosts file by default but handles this internally
        // To compensate for this, we explicitly use hard-coded defaults for localhost
        if (DIRECTORY_SEPARATOR === '\\') {
            $executor = new HostsFileExecutor(
                new HostsFile("127.0.0.1 localhost\n::1 localhost"),
                $executor
            );
        }

        return $executor;
    }

    private function createExecutor($nameserver, LoopInterface $loop)
    {
        $parts = \parse_url($nameserver);

        if (isset($parts['scheme']) && $parts['scheme'] === 'tcp') {
            $executor = $this->createTcpExecutor($nameserver, $loop);
        } elseif (isset($parts['scheme']) && $parts['scheme'] === 'udp') {
            $executor = $this->createUdpExecutor($nameserver, $loop);
        } else {
            $executor = new SelectiveTransportExecutor(
                $this->createUdpExecutor($nameserver, $loop),
                $this->createTcpExecutor($nameserver, $loop)
            );
        }

        return new CoopExecutor($executor);
    }

    private function createTcpExecutor($nameserver, LoopInterface $loop)
    {
        return new TimeoutExecutor(
            new TcpTransportExecutor($nameserver, $loop),
            5.0,
            $loop
        );
    }

    private function createUdpExecutor($nameserver, LoopInterface $loop)
    {
        return new RetryExecutor(
            new TimeoutExecutor(
                new UdpTransportExecutor(
                    $nameserver,
                    $loop
                ),
                5.0,
                $loop
            )
        );
    }
}
