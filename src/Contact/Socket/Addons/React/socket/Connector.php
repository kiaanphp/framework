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
use Kiaan\Contact\Socket\Addons\React\Dns\Config\Config as DnsConfig;
use Kiaan\Contact\Socket\Addons\React\Dns\Resolver\Factory as DnsFactory;
use Kiaan\Contact\Socket\Addons\React\Dns\Resolver\ResolverInterface;
use Kiaan\Contact\Socket\Addons\React\EventLoop\LoopInterface;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
final class Connector implements ConnectorInterface
{
    private $connectors = array();

    public function __construct(LoopInterface $loop, array $options = array())
    {
        // apply default options if not explicitly given
        $options += array(
            'tcp' => true,
            'tls' => true,
            'unix' => true,

            'dns' => true,
            'timeout' => true,
            'happy_eyeballs' => true,
        );

        if ($options['timeout'] === true) {
            $options['timeout'] = (float)\ini_get("default_socket_timeout");
        }

        if ($options['tcp'] instanceof ConnectorInterface) {
            $tcp = $options['tcp'];
        } else {
            $tcp = new TcpConnector(
                $loop,
                \is_array($options['tcp']) ? $options['tcp'] : array()
            );
        }

        if ($options['dns'] !== false) {
            if ($options['dns'] instanceof ResolverInterface) {
                $resolver = $options['dns'];
            } else {
                if ($options['dns'] !== true) {
                    $server = $options['dns'];
                } else {
                    // try to load nameservers from system config or default to Google's public DNS
                    $config = DnsConfig::loadSystemConfigBlocking();
                    $server = $config->nameservers ? \reset($config->nameservers) : '8.8.8.8';
                }

                $factory = new DnsFactory();
                $resolver = $factory->createCached(
                    $server,
                    $loop
                );
            }

            if ($options['happy_eyeballs'] === true) {
                $tcp = new HappyEyeBallsConnector($loop, $tcp, $resolver);
            } else {
                $tcp = new DnsConnector($tcp, $resolver);
            }
        }

        if ($options['tcp'] !== false) {
            $options['tcp'] = $tcp;

            if ($options['timeout'] !== false) {
                $options['tcp'] = new TimeoutConnector(
                    $options['tcp'],
                    $options['timeout'],
                    $loop
                );
            }

            $this->connectors['tcp'] = $options['tcp'];
        }

        if ($options['tls'] !== false) {
            if (!$options['tls'] instanceof ConnectorInterface) {
                $options['tls'] = new SecureConnector(
                    $tcp,
                    $loop,
                    \is_array($options['tls']) ? $options['tls'] : array()
                );
            }

            if ($options['timeout'] !== false) {
                $options['tls'] = new TimeoutConnector(
                    $options['tls'],
                    $options['timeout'],
                    $loop
                );
            }

            $this->connectors['tls'] = $options['tls'];
        }

        if ($options['unix'] !== false) {
            if (!$options['unix'] instanceof ConnectorInterface) {
                $options['unix'] = new UnixConnector($loop);
            }
            $this->connectors['unix'] = $options['unix'];
        }
    }

    public function connect($uri)
    {
        $scheme = 'tcp';
        if (\strpos($uri, '://') !== false) {
            $scheme = (string)\substr($uri, 0, \strpos($uri, '://'));
        }

        if (!isset($this->connectors[$scheme])) {
            return \Kiaan\Contact\Socket\Addons\React\Promise\reject(new \RuntimeException(
                'No connector available for URI scheme "' . $scheme . '"'
            ));
        }

        return $this->connectors[$scheme]->connect($uri);
    }
}

