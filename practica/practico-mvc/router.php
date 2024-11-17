<?php

require_once 'EjercicioControlador.php';
$action = "home";
if(isset($_GET['action'])){
    $action = $_GET['action'];
}

$params = explode($action, '');

switch ($action) {
    case 'home':
        break;
    case 'productos':
        $controlador = new EjercicioControlador();
        $controlador->mostrarProductos();   
        break;
    
    default:
        # code...
        break;
}

