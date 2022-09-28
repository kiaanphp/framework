<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Security\Validator\Traits;

/*
|---------------------------------------------------
| Trait
|---------------------------------------------------
*/
trait PdoTrait
{

    /** @var \PDO */
    protected $pdo;

    /**
     * Get PDO
     *
     * @return array
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Set PDO
     *
     * @return array
     */
    public function setPdo($pdo)
    {
        return $this->pdo = $pdo;
    }

}
