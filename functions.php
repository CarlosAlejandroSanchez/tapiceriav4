<?php
@ob_start();
session_start();

use Illuminate\Database\Capsule\Manager as DB;
require 'vendor/autoload.php';
require 'config/database.php';

$userstr = "Bienvenido: invitado";
if(isset($_SESSION['user'])){
    $user     = $_SESSION['user'];
    $loggedin = TRUE;

    $users = DB::table('usuarios')->where('usuario','=',$user)->first();
    $id = $users->id_usuario;

    $saldo = DB::table('perfil')->where('usuarios_id_usuario', $id)->first();
    $dinero = "Dinero: $$saldo->dinero";

    $userstr = "Bienvenido: $user";

    if($saldo->rol == 1)
    {
        $userstr = "Bienvenido dueño: $user";
    }

    $linkloggedin = '
    <a href="perfil.php" class="navbar-item">
        Perfil
    </a>
    <a href="logout.php" class="navbar-item">
        Cerrrar Sesión
    </a>
    ';
}else{
    $loggedin = FALSE;
    $dinero = "";
    $linkloggedin = '
    <a href="login.php" class="navbar-item">
        iniciar sesión
    </a>
    <a href="singup.php" class="navbar-item">
        Regístrate
    </a>
    ';
}

echo '
<!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <link rel="stylesheet" href="node_modules/bulma/css/bulma.min.css">
        <script src="node_modules/axios/dist/axios.min.js"></script>

        <link rel="stylesheet" href="css/style.css">

        <title>'.$userstr.'</title>
    </head>
    ';

function destroySession()
{
    if (session_id() != "" || isset($_COOKIE[session_name()]))
        setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
}

function sanitizeString($var)
{
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}