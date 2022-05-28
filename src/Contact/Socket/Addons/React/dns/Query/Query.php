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
| Class
|---------------------------------------------------
*/
final class Query
{
    /**
     * @var string query name, i.e. hostname to look up
     */
    public $name;

    /**
     * @var int query type (aka QTYPE), see Message::TYPE_* constants
     */
    public $type;

    /**
     * @var int query class (aka QCLASS), see Message::CLASS_IN constant
     */
    public $class;

    /**
     * @param string $name  query name, i.e. hostname to look up
     * @param int    $type  query type, see Message::TYPE_* constants
     * @param int    $class query class, see Message::CLASS_IN constant
     */
    public function __construct($name, $type, $class)
    {
        $this->name = $name;
        $this->type = $type;
        $this->class = $class;
    }
}
