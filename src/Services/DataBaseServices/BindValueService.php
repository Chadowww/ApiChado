<?php

namespace App\Services\DataBaseServices;

use PDOStatement;

class BindValueService
{
    public function bindValuesToStatement(PDOStatement $statement, array $values): void
    {
        foreach($values as $param => $value){
            $statement->bindValue($param, $value);
        }
    }
}