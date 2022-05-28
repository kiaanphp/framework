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
namespace Kiaan\Database;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use PDO;
use PDOException;
use Exception;

/*
|---------------------------------------------------
| Schema
|---------------------------------------------------
*/
class Schema {


    /**
    * Traits
    *
    */
    use Schema\FunctionsTrait;

    /*
    * Connect class
    */
    public $connectClass;

    /*
    * Sql class
    */
    public $sqlClass;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($connectionString=null) {
        $this->sqlClass = (new Schema\Sql);
        $this->connectClass = $connectionString;
    }
    
    /**
     * Get connect
    */
    public function getConnect()
    {
        return $this->connectClass;
    }

    /**
     * Set connect
    */
    public function setConnect($pdo)
    {
        $this->sqlClass = (new Schema\Sql);
        return $this->connectClass = $pdo;
    }

    /**
     * execute Query
     *
    */
    public function execute($sql, $parms=[], $fetch="obj")
    {

        // fetch type
        switch ($fetch) {
            case 'obj':
                $fetch = PDO::FETCH_OBJ;
                break;

            case 'num':
                $fetch = PDO::FETCH_NUM;
                break;
                
            case 'name':
                $fetch = PDO::FETCH_NAMED;
                break;
                
            case 'lazy':
                $fetch = PDO::FETCH_LAZY;
                break;
                
            case 'into':
                $fetch = PDO::FETCH_INTO;
                break;

            case 'class':
                $fetch = PDO::FETCH_CLASS;
                break;

            case 'bound':
                $fetch = PDO::FETCH_BOUND;
                break;

            case 'both':
                $fetch = PDO::FETCH_BOTH;
                break;

            case 'assoc':
                $fetch = PDO::FETCH_ASSOC;
                break;

            default:
                $fetch = PDO::FETCH_OBJ;
                break;
        }

        // excute
        $excute = $this->connectClass->prepare($sql);
        $excute->execute($parms); 

        try {
            $excute = $excute->fetchAll($fetch);
        } catch (\Throwable $th) {
        }

        return $excute;
    }

}