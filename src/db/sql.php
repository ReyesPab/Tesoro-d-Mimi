<?php

namespace App\db;

use App\config\responseHTTP;
use PDOException;

class sql extends connectionDB {
        // construimos un metodo que me permitira verificar si existe un registro en nuestra base de datos bajo ciertos parametros y condiciones
        public static function verificarRegistro($sql, $condicion, $params){
            try {
                // abrimos la conexion
                $con = self::getConnection();
                $query = $con->prepare($sql); // preparamos la consulta que viene en el parametro $sql
                $query->execute([
                    $condicion => $params   // pasamos la condicion de la consulta y los parametros correspondientes a traves de un array asociativo
                ]);                         // select nombre from tbl_usuario where id = 12345
                
                // ahora recorremos y contamos los datos retornados
                $res = ($query->rowCount() == 0) ? false : true; // esto es lo mismo que este haciendo un
                                                                 // if($query->rowCount() == 0){false}else{true}
                return $res; // retornamos la respuesta
            } catch (\PDOException $e) {
            error_log("sql::verificarRegistro -> Error en la consulta: " . $e->getMessage());
            die(json_encode(responseHTTP::status500('Error en la base de datos: ' . $e->getMessage())));
        }
    }
}