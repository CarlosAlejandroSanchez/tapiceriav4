<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as DB;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

// Instantiate app
$app = AppFactory::create();
$app->setBasePath("/tapiceriav4/api/index.php");

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->post('/registrar', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $usuario = $data->usuario;
    $contraseña = $data->contraseña;
    $Rcontraseña = $data->Rcontraseña;

    $msg = new stdClass();

    //Validamos que las variables no estén vacías
    if($usuario == "" || $contraseña == "" || $Rcontraseña == "")
    {
        $msg->mensaje = "Faltan datos";
    }
    else
    {
        //Se validan que las contraseñas sean iguales
        if ($contraseña != $Rcontraseña)
        {
            $msg->mensaje = "Las contraseñas no son iguales";
        }
        else
        {
            $users = DB::table('usuarios')->where('usuario', $usuario)->first();

            //Se comprueba si existe un user con las condiciones puestas
            if($users)
            {
                $msg->mensaje = "ese usuario ya existe, elige otro";
            }
            else
            {
                $usuario = DB::table('usuarios')->insertGetId(
                    ['usuario' => $usuario, 'contraseña' => $contraseña]
                );

                $id_usuario = DB::table('usuarios')->max('id_usuario');

                $datos = DB::table('perfil')->insert(
                    ['usuarios_id_usuario'=>$id_usuario, 'dinero'=>0, 'rol'=>2]
                );

                if($usuario && $datos)
                {
                    $msg->mensaje = "Cuenta registrada, por favor, inicia sesión";
                }
                else
                {
                    $msg->mensaje = "algo ha salido mal";
                }
            }
        }
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});


$app->post('/login', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $login = DB::table('usuarios')->where('usuario', $data->usuario)->first();

    $msg = new stdClass();

    if($login)
    {
        if($login->contraseña == $data->contraseña)
        {
            $msg->acceso = 1;
            $msg->mensaje = "Bienvenido: " . $login->usuario;
            $msg->usuario = $login->usuario;
        }
        else {
            $msg->acceso = 0;
            $msg->mensaje = "contraseña incorrecta";
        }
    }
    else {
        $msg->mensaje = "cuenta y/o contraseña incorrectas";
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});

$app->get('/logear/{usuario}', function (Request $request, Response $response, array $args) {

    $msg = "Procesando...";

    require_once '../functions.php';

    $_SESSION['user'] = $args['usuario'];
    echo'<meta http-equiv="Refresh" content="0;url=../../../index.php">';

    $response->getBody()->write($msg);
    return $response;
});

$app->post('/cita', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $id_servicio = $data->servicio;
    $servicios = DB::table('servicios')->where('id_servicio', $id_servicio)->first();

    $id = $data->id_usuario;

    $saldo = DB::table('perfil')->where('usuarios_id_usuario', $id)->first();

    $msg = new stdClass();

    if($saldo->dinero >= $servicios->precio)
    {
        $msg->aprobado = "aprobado";
        $fecha = $data->fecha;

        if($fecha != "")
        {
            $msg->fecha = "aprobado";

            $cita = DB::table('citas')->insertGetId(
                ['usuarios_id_usuario'=>$id]
            );
            if($cita)
            {
                $sacar = DB::table('citas')->max('id_cita');

                $id_cita = $sacar;
                DB::table('citas_servicios')->insert(
                    ['servicios_id_servicio'=>$id_servicio, 'citas_id_cita'=>$id_cita, 'fecha'=>$fecha, 'status'=>'Pendiente']
                );

                $nuevo_saldo = $saldo->dinero - $servicios->precio;

                $actualizar = DB::table('perfil')
                ->where('usuarios_id_usuario', $id)
                ->update(['dinero'=>$nuevo_saldo]);

                $msg->mensaje = "cita añadida con éxito";
            }
            else
            {
                $msg->mensaje = "upss algo ha salido mal";
            }
        }
        else
        {
            $msg->fecha = "Por favor, agregue una fecha";
        }
    }
    else
    {
        $msg->aprobado = "Saldo insuficiente";
    }


    $response->getBody()->write(json_encode($msg));
    return $response;
});

$app->post('/saldo', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $usuario = DB::table('usuarios')->where('usuario', $data->usuario)->first();

    $msg = new stdClass();

    if($usuario)
    {
        if($usuario->contraseña == $data->contraseña)
        {
            $msg->mensaje = "Quieres añadir: " . $data->saldo;
        }
        else {
            $msg->mensaje = "contraseña incorrecta";
        }
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});

$app->post('/', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $msg = new stdClass();



    $response->getBody()->write(json_encode($msg));
    return $response;
});


// Run application
$app->run();