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
namespace Kiaan\Database\Schema;

/*
|---------------------------------------------------
| Create column
|---------------------------------------------------
*/
class CreateColumn extends Columns {
    
    // Submit
    public function submit(){
        $sql = ($this->driver)->createColumnSql($this->table, $this->columns);
        $this->schema->execute($sql);

        return true;
    }
}