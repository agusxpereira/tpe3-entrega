<?php

require_once './app/modelos/generos.modelo.php';
require_once './app/vistas/jsonview.php';


class GenerosApiController{

    private $modelo;
    private $vista;

    public function __construct(){
        $this->modelo = new generosModelo();
        $this->vista = new JSONView();
        //lo mismo para las vistas
        
   
    }

    public function obtenerGeneros($req, $res){
      
        $activo = null;
        $pagina = false;
        $limite = 10;
        $ordenarPor = false;
        $orden=false;

        if(isset($req->query->activo) && ($req->query->activo == "true"))
            //api/generos?ofertas=true
            $activo = true;
            else if (isset($req->query->activo) && $req->query->activo == "false")  $activo = false;
            
        
        if(isset($req->query->ordenarPor) && ($req->query->ordenarPor != "")){
            //api/generos?ordenarPor="precio"
            $ordenarPor = $req->query->ordenarPor;
            

            if(isset($req->query->orden) &&($req->query->orden) != "" ){
                $orden = $req->query->orden;
            }
        
        }
        if(isset($req->query->pagina) && ($req->query->pagina != "")){
            $pagina = $req->query->pagina;
            
        }
        if(isset($req->query->limite) && ($req->query->limite != "")){
            $limite = $req->query->limite;                
        }
        
        try {
            
            $generos = $this->modelo->obtenergeneros($activo, $ordenarPor, $orden, $pagina, $limite);
            return $this->vista->response($generos);//tiene como defecto 200
        } catch (Exception $e) {
            // Manejo de error: lo mostramos en la respuesta
            return $this->vista->response(['error' => $e->getMessage()], 500);
        }
        
    }
    public function obtenergenero($req, $res){
        // api/generos/:id
        $id = $req->params->id; // obtenemos el ID de la request: todavía no se en que momento se crea el request
        //en la clase dice que viene de la ruta
        //creo que se refiere a que lo inicializa el constructor complejo cuando accedemos a una ruta
        $genero = $this->modelo->obtenerGeneroPorId($id);
        
        if(!$genero){
            return $this->vista->response("No exisitia un genero para el id=$id",404);
        }
        
        return $this->vista->response($genero);
    }
    public function borrargenero($req, $res){
        $id = $req->params->id;

        $genero = $this->modelo->obtenergeneroPorId($id);

        if(!$genero){
            return $this->vista->response("El genero con el id=$id no existe.", 404);
        }

        $this->modelo->eliminargenero($id);
        return $this->vista->response("El genero con el id=$id fue borrado con exito.");
    }
    private function procesarImagen()
    { //devuelve una ruta de imagen si existe un archivo. sino retorna null. 
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $tipo = $_FILES['foto']['type'];
            if (($tipo == "image/jpg" || $tipo == "image/jpeg" || $tipo == "image/png")) {
                $archivo = $_FILES['foto']['name'];
                if ($archivo <> null) {
                    $informacion = pathinfo($archivo);
                    $fechaHoraActual = date('Y-m-d_H-i-s-ms');
                    $ruta = "img/" . $informacion['filename'] . $fechaHoraActual . '.' . $informacion['extension'];
                    move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);
                    return $ruta;
                }
            }
        }
        return null;
    }
      
    public function agregarGenero($req, $res) {
        // Verificar que se recibió el nombre
        if (!isset($req->body->nombre) || empty($req->body->nombre)) {
            return $this->vista->response(['error' => 'Falta completar el nombre'], 400);
        }
        if (!isset($req->body->descripcion) || empty($req->body->descripcion)) {
            return $this->vista->response(['error' => 'Falta completar el descripción'], 400);
        }
        // Procesar la imagen
        $ruta_imagen = $this->procesarImagen();
    
        // Recibir otros datos
        $nombre = $req->body->nombre;
        $descripcion = $req->body->descripcion ?? null;
    
        // Insertar en la base de datos
        $id = $this->modelo->agregarGenero($nombre, $descripcion, $ruta_imagen);
    
        if ($id > 0) {
            return $this->vista->response([
                'id' => $id,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'ruta_imagen' => $ruta_imagen,
                "activo" =>true
            ], 201);
        } else {
            if ($ruta_imagen) {
                unlink($ruta_imagen);
            }
            return $this->vista->response(['error' => "El género $nombre no se pudo insertar. Es posible que ya exista un género con el mismo nombre."], 500);
        }
    }

    public function editarGenero($req, $res) {
        $id = $req->params->id;
        // Verificar si el género existe
        $genero = $this->modelo->obtenerGeneroPorId(intval($id));
        if (!$genero) {
            return $this->vista->response(['error' => "No existe el género con el id=$id"], 404);
        }
    
        // Procesar la imagen en caso de ser necesario
        $ruta_imagen = $genero->ruta_imagen; // Mantener la ruta actual por defecto
        if (isset($_FILES['foto']) && !empty($_FILES['foto']['name'])) {
            $nueva_ruta = $this->procesarImagen();
            if ($nueva_ruta) {
                $ruta_imagen = $nueva_ruta; // Actualizar la ruta de la imagen
            } else {
                return $this->vista->response(['error' => 'Error al procesar la nueva imagen'], 500);
            }
        }
    
        // Obtener los datos del cuerpo de la solicitud
        $nombre = $req->body->nombre ?? $genero->nombre; // Usar el valor actual si no se envía uno nuevo
        $descripcion = $req->body->descripcion ?? $genero->descripcion;
    
        try {
            // Editar el género en la base de datos
            $this->modelo->editarGenero(intval($id), $nombre, $descripcion, $ruta_imagen,true);
    
            // Si se actualizó la imagen, eliminar la anterior
            if (isset($_FILES['foto']) && !empty($_FILES['foto']['name']) && $genero->ruta_imagen) {
                unlink($genero->ruta_imagen);
            }
    
            // Responder con éxito
            return $this->vista->response([
                'id' => $id,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'ruta_imagen' => $ruta_imagen,
                "activo" =>true
            ], 200);
        } catch (PDOException $e) {
            // Responder con error en caso de fallo
            return $this->vista->response(['error' => "No se pudo editar el género $genero->nombre."], 500);
        }
    }
    
    public function activarGenero($req, $res) {
        $id = $req->params->id;
        // Verificar si el género existe
        $genero = $this->modelo->obtenerGeneroPorId(intval($id));
        if (!$genero) {
            return $this->vista->response(['error' => "No existe el género con el id=$id"], 404);
        }
    
        try {
            // Alternar el estado del género (activar/desactivar)
            $nuevoEstado = !$genero->activo; // Cambiar al estado opuesto
            $this->modelo->activarGenero($id, $nuevoEstado);
    
            // Responder con éxito
            return $this->vista->response([
                'id' => $id,
                'nombre' => $genero->nombre,
                'estado' => $nuevoEstado ? 'activo' : 'inactivo'
            ], 200);
        } catch (PDOException $e) {
            // Responder con error en caso de fallo
            return $this->vista->response(['error' => "No se pudo cambiar el estado del género $genero->nombre."], 500);
        }
    }
    

}