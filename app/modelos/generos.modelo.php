<?php
require_once 'Modelo.base.php';
class GenerosModelo extends ModeloBase{

    /*Funcion de ayuda*/
    public function obtenerIdGenero($nombre){
       $query =  $this->db->prepare("SELECT id FROM generos WHERE nombre = ?");
       $query->execute([$nombre]);
       $id = $query->fetchAll(PDO::FETCH_COLUMN);
       
       return $id[0];
    
    }

    public function obtenerGeneros($filtrarActivo = null, $ordenarPor = false, $orden = false, $pagina = false, $limite = false){
        $sql = "SELECT * FROM generos";
        //alteeramos el pedido según el parametro
        if(isset($filtrarActivo)){
            if($filtrarActivo == true ){
                $sql = $sql." WHERE activo = 1";
            } else if($filtrarActivo == false ){
                $sql = $sql." WHERE activo = 0";
            }

        }
        if ($ordenarPor) {
            switch ($ordenarPor) {
                case 'nombre':
                    $sql .= " ORDER BY nombre";
                    break;
        
                case 'descripcion':
                    $sql .= " ORDER BY descripcion";
                    break;
        
                case 'activo':
                    $sql .= " ORDER BY activo";
                    break;
        
                case 'fecha_actualizacion':
                    $sql .= " ORDER BY fecha_actualizacion";
                    break;
            }
        }
        

        if($orden && $ordenarPor != false){
            switch ($orden) {
                case 'ascendente':
                    $sql .= " ASC";
                    break;
                
                case 'descendente':
                    $sql .= " DESC";
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        var_dump($sql);
        $consulta = $this->db->prepare($sql);
        $consulta->execute();
        $generos = $consulta->fetchAll(PDO::FETCH_OBJ);
      

        $generosLimitados = [];
        if(isset($pagina) && $pagina!=false){
            if(!isset($limite) || $limite == false)
                $limite = 10;

            if (count($generos) > $limite) {
                
                //    13          10
                $final =  $limite * $pagina;//10 * 2
                $inicio = $final - $limite;//10

                if($final > count($generos))
                    $final = count($generos);
                    
                    for($i = $inicio; $i < $final; $i++){
                        array_push($generosLimitados, $generos[$i]);
                    }
                
            }
        }
        if($generosLimitados != []){
            return $generosLimitados;
        }

        return $generos;
    }
    public function agregarGenero($nombre, $descripcion, $ruta_imagen)
    {//con default true deberia tener en cuenta siempre el activo y la fecha
        //Por defecto se crea activo
        try {
            $consulta = $this->db->prepare('INSERT INTO generos(nombre, descripcion, Ruta_imagen) VALUES (?, ?, ?)');
            $consulta->execute([$nombre, $descripcion, $ruta_imagen]);
            $id = $this->db->lastInsertId();
        } catch (\Throwable $th) {
            $id = -1;
        }
        return $id;
    }
    //editar
    public function editarGenero($id, $nombre, $descripcion, $ruta_imagen,$activo)
    {
        $consulta = $this->db->prepare('UPDATE generos SET nombre= ? , descripcion=? , ruta_imagen = ?, activo = ? WHERE id = ?');

        $consulta->execute([$nombre, $descripcion, $ruta_imagen,$activo, $id]);
        return $consulta->rowCount();
    }
    public function activarGenero($id, $activo)
    {
        $consulta = $this->db->prepare('UPDATE generos SET activo = ? WHERE id = ?');

        $consulta->execute([$activo, $id]);
        return $consulta->rowCount();
    }
   //Eliminar
    public function eliminarGenero($id)
    {

            $genero = $this->obtenerGeneroPorId($id);

            $consulta = $this->db->prepare('DELETE FROM generos WHERE id = ?');

            $consulta->execute([$id]);
            return $consulta->rowCount();

    }
    public function obtenerGeneroPorId($id)
    {
        $consulta = $this->db->prepare("SELECT * FROM generos WHERE id = ?");
        $consulta->execute([$id]);
        $genero = $consulta->fetch(PDO::FETCH_OBJ);
        
        return $genero;
    }
}
