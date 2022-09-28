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

/*
|---------------------------------------------------
| PDo Database
|---------------------------------------------------
*/
class pdoDb {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /* 
    * PDO
    */
    protected $pdo;

    /**
     * Connect
     * 
     * Connect to Database
    */
    public function connect($connectionString, $getError=false) {
        $driver = $connectionString['driver'] ?? null;
        $host = $connectionString['host'] ?? null;  
        $dbname = $connectionString['db'] ?? null;  
        $port = $connectionString['port'] ?? null;  
        $username = $connectionString['user'] ?? null;  
        $password = $connectionString['pass'] ?? null;

        $option = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
           ];

        // Generate connection string
        switch ($driver) {
            case 'mysql':
                $string = "mysql:host=$host;dbname=$dbname;charset=utf8mb4;port=$port";
            break;

            case 'sqlite':
                $string = "sqlite:$host";
            break;

            case 'pgsql':
                $string = "pgsql:host=$host;dbname=$dbname;port=$port";
                break;

            case 'sqlsrv':
                $string = "sqlsrv:Server=$host;Database=$dbname";
                break;

            default:
                $string = "mysql:host=$host;dbname=$dbname;charset=utf8mb4;port=$port";
            }

            try {
                    $pdo = new \PDO($string, $username, $password, $option);
                } catch (\PDOException $e) {
                    if($getError){
                        throw new \PDOException($e->getMessage(), (int)$e->getCode());
                    }
                    $pdo = null;
            }

            // Set connection
            $this->pdo = $pdo;
            
            // Return connection
            return $pdo;
        }

        /**
         * Connection
         * 
         * Get a connection.
        */
        public function connection() {
            return $this->pdo;
        }

        /**
         * Get connect
        */
        public function getConnect()
        {
            return $this->pdo;
        }

        /**
         * Set connect
        */
        public function setConnect($pdo)
        {
            // PDO class
            return $this->pdo = $pdo;
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
            $excute = $this->pdo->prepare($sql);
            $excute->execute($parms); 

            try {
                $excute = $excute->fetchAll($fetch);
            } catch (\Throwable $th) {
            }

            return $excute;
        }

}