<?php
use Illuminate\Database\Capsule\Manager as DB;
require_once 'header.php';

$error = $result = $dinero_A = "";

if(!empty($_POST['saldo']))
{
    $dinero = $_POST['saldo'];
    if($dinero > 0)
    {
        $pass = $_POST['contraseña'];
        $validar = DB::table('usuarios')
            ->leftJoin('perfil', 'usuarios.id_usuario', '=', 'perfil.usuarios_id_usuario')
            ->where('id_usuario', $id)->where('usuarios_id_usuario', $id)->where('contraseña', $pass)
            ->first();

        if($validar)
        {
            $nuevo_saldo = $validar->dinero + $dinero;

            $actualizar = DB::table('perfil')
                ->where('usuarios_id_usuario', $id)
                ->update(['dinero'=>$nuevo_saldo]);

            if($actualizar)
            {
                $saldo = DB::table('perfil')->where('usuarios_id_usuario', $id)->first();
                $result = die('<p class="center mt-6 is-size-3">
                    Saldo agregado: '.$dinero.'
                    <br>
                    Nuevo saldo: '.$saldo->dinero.'
                <p>');
            }
            else
            {
                echo'ah?';
            }
        }
        else
        {
            $error = "Contraseña inválida";
            $dinero_A = $dinero;
        }
    }
    else
    {
        $error = "Debe elegir una cantidad válida";
    }
}
if(!empty($_GET['id_cancelar']))
{
    $id_cita = $_GET['id_cancelar'];
    $cita = DB::table('citas')
    ->leftJoin('citas_servicios', 'citas_servicios.citas_id_cita', '=', 'citas.id_cita')
    ->where('id_cita', $id_cita)
    ->first();

    if($cita->status == "Pendiente")
    {
        DB::table('citas_servicios')
        ->where('citas_id_cita', $id_cita)
        ->update(['status'=>'Cancelada']);


        $ayuda = DB::table('servicios')
        ->where('id_servicio',$cita->servicios_id_servicio)
        ->first();

        $perfil = DB::table('perfil')
        ->where('usuarios_id_usuario', $cita->usuarios_id_usuario)
        ->first();

        $dinero_actual = $perfil->dinero;

        $suma = $dinero_actual + $ayuda->precio;

        DB::table('perfil')
        ->where('usuarios_id_usuario', $cita->usuarios_id_usuario)
        ->update(['dinero'=>$suma]);

        die('<p class="center mt-6 is-size-4">Cita cancelada</p>
        <p class="center mt-4 is-size-4">Se ha devuelto el dinero a su cuenta</p>');
    }
    else {
        die('<p class="center mt-6 is-size-4">Esa cita ya ha sido cancelada</p>');
    }
}
elseif(!empty($_GET['id_confirmar']))
{
    $id_cita = $_GET['id_confirmar'];
    $cita = DB::table('citas_servicios')
    ->where('citas_id_cita', $id_cita)
    ->first();

    if($cita->status == "Pendiente")
    {
        DB::table('citas_servicios')
        ->where('citas_id_cita', $id_cita)
        ->update(['status'=>'Confirmada']);
        die('<p class="center mt-6 is-size-4">Cita confirmada</p>');
    }else {
        die('<p class="center mt-6 is-size-4">Esa cita ya ha sido Confirmada</p>');
    }
}

if (!$loggedin) die("<meta http-equiv='Refresh' content='0;url=index.php'></div></body></html>");

echo '
        <div class="container is-fluid">
';

if($saldo->rol != 1)
{
    echo'
            '.$result.'
            <p class="center is-size-1">Perfil de: '.$user.'</p>
            <div class="card mt-6">
                <header class="card-header">
                    <p class="card-header-title">
                        Agrega saldo a tu cuenta!
                    </p>
                    <a href="#" class="card-header-icon" aria-label="more options">
                        <span class="icon">
                            <i class="fas fa-angle-down" aria-hidden="true"></i>
                        </span>
                    </a>
                </header>
                <div class="card-content">
                    <div class="content">
                        <form method="post" action="perfil.php">
                            <div class="field">
                                <p class="center is-size-4 mb-2">'.$error.'</p>
                                <label class="label mt-4">Saldo a agregar</label>
                                <div class="control">
                                    <input class="input" type="text" maxlength="45" id="saldo" name="saldo" placeholder="Saldo a agregar" value="'.$dinero_A.'">
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Contraseña</label>
                                <div class="control">
                                    <input class="input" type="password" maxlength="45" name="contraseña" placeholder="Contraseña">
                                </div>
                            </div>
                            <button type="submit" class="button is-link mt-3">Agregar</button>
                        </form>
                    </div>
                </div>
            </div>
            ';
}

            if($saldo->rol == 1)
            {
                $cita = DB::table('citas_servicios')
                ->leftJoin('citas', 'citas_servicios.citas_id_cita', '=', 'citas.id_cita')
                ->orderBy('id_cita')
                ->get();
            }
            else
            {
                $cita = DB::table('citas_servicios')
                ->leftJoin('citas', 'citas_servicios.citas_id_cita', '=', 'citas.id_cita')
                ->where('usuarios_id_usuario', $id)
                ->orderBy('id_cita')
                ->get();
            }

            echo'
            <div class="card mt-6 mb-4">
                <header class="card-header">
                    <p class="card-header-title">
                        Cancela tu(s) cita(s)
                    </p>
                    <a href="#" class="card-header-icon" aria-label="more options">
                        <span class="icon">
                            <i class="fas fa-angle-down" aria-hidden="true"></i>
                        </span>
                    </a>
                </header>
                <div class="card-content">
                    <div class="content">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><abbr title="cita">No. Cita</abbr></th>
                                    <th><abbr title="user">Usuario</abbr></th>
                                    <th><abbr title="servicio">Servicio</abbr></th>
                                    <th><abbr title="precio">Precio</abbr></th>
                                    <th><abbr title="fecha">Fecha</abbr></th>
                                    <th><abbr title="status">Status</abbr></th>
                                    <th><abbr title="action">Eliminar</abbr></th>
                                    <th><abbr title="action2">Confirmar</abbr></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th><abbr title="cita">No. Cita</abbr></th>
                                    <th><abbr title="user">Usuario</abbr></th>
                                    <th><abbr title="servicio">Servicio</abbr></th>
                                    <th><abbr title="precio">Precio</abbr></th>
                                    <th><abbr title="fecha">Fecha</abbr></th>
                                    <th><abbr title="status">Status</abbr></th>
                                    <th><abbr title="action">Eliminar</abbr></th>
                                    <th><abbr title="action2">Confirmar</abbr></th>
                                </tr>
                            </tfoot>
                            <tbody>';
                            foreach($cita as $a)
                            {
                                $cita_cliente = $user;
                                if($saldo->rol == 1)
                                {
                                    $nombre_cita = DB::table('usuarios')->where('id_usuario', $a->usuarios_id_usuario)->first();
                                    $cita_cliente = $nombre_cita->usuario;
                                }
                                $servicio = DB::table('servicios')->where('id_servicio', $a->servicios_id_servicio)->first();
                                echo'
                                <tr>
                                    <td>'.$a->id_cita.'</td>
                                    <td>'.$cita_cliente.'</td>
                                    <td>'.$servicio->nombre.'</td>
                                    <td>$'.$servicio->precio.'</td>
                                    <th>'.$a->fecha.'</th>
                                    <td>'.$a->status.'</td>
                                ';
                                if($a->status == 'Pendiente')
                                {
                                echo'
                                    <td><a class="button is-link" href="perfil.php?id_cancelar='.$a->id_cita.'">Cancelar</a></td>
                                    <td><a class="button is-link" href="perfil.php?id_confirmar='.$a->id_cita.'">Comfirmar</a></td>
                                </tr>
                                ';
                                }
                            }
                            echo'
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener("load", function() {
                saldo.addEventListener("keypress", soloNumeros, false);
            });
            //Solo permite introducir numeros.
            function soloNumeros(x){
                var key = window.event ? x.which : x.keyCode;
                if (key < 47 || key > 57) {
                    x.preventDefault();
                }
            }
        </script>

    </body>
</html>
';