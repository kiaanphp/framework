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
namespace Kiaan\Database\DB;

/*
|---------------------------------------------------
| Connection trait
|---------------------------------------------------
*/
trait ConnectionTrait {

    /**
     * Set connect
    */
    public function setConnect($pdo)
    {
        // PDO class
        return $this->pdo = $pdo;
    }

    /**
     * Get connect
    */
    public function getConnect()
    {
        return $this->pdo;
    }

}