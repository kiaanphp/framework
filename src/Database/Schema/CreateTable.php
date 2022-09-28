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
| Create table
|---------------------------------------------------
*/
class CreateTable extends Columns {

    // Submit
    public function submit(){
        $sql = ($this->driver)->createTableSql($this->table, $this->columns);

        $this->schema->execute($sql);

        return true;
    }
}