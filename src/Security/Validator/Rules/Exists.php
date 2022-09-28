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
namespace Kiaan\Security\Validator\Rules;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Security\Validator\Rule;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Exists extends Rule
{
    protected $message = ":attribute :value has been not found";

    protected $fillableParams = ['table', 'column'];

    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);

        // getting parameters
        $table = $this->parameter('table');
        $column = $this->parameter('column');

        // do query
        $stmt = $this->pdo->prepare("select count(*) as count from {$table} where {$column} = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        // true for valid, false for invalid
        return intval($data['count']) > 0;
    }
}
