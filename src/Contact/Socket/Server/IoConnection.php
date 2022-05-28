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
namespace Kiaan\Contact\Socket\Server;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\ConnectionInterface;
use Kiaan\Contact\Socket\Addons\React\Socket\ConnectionInterface as ReactConn;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class IoConnection implements ConnectionInterface {
    /**
     * @var \React\Socket\ConnectionInterface
     */
    protected $conn;


    /**
     * @param \React\Socket\ConnectionInterface $conn
     */
    public function __construct(ReactConn $conn) {
        $this->conn = $conn;
    }

    /**
     * {@inheritdoc}
     */
    public function send($data) {
        $this->conn->write($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        $this->conn->end();
    }
}
