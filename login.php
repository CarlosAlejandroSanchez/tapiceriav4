<?php
require_once 'header.php';

echo'
<body>
    <div class="container is-fluid">
';

if(!$loggedin){

    if(isset($_POST['usuario']))
    {
        $usuario = sanitizeString($_POST['usuario']);
        $contraseña = sanitizeString($_POST['contraseña']);

        $users = DB::table('usuarios')->select(['usuario', 'contraseña'])->where('usuario', $usuario)->where('contraseña', $contraseña)->first();

        if (!$users)
        {
            $error = '<p class="is-size-5">cuenta o contraseña inválida</p>';
        }
        else
        {
            $_SESSION['user'] = $usuario;
            $_SESSION['pass'] = $contraseña;

            die('<p class="is-size-4 center mt-6">Haz iniciado sesión, <a href="index.php">click aquí</a> para ir al sistema</p>
            </div></body></html>');
        }
    }

    echo'
        <div class="card mt-6">
            <header class="card-header">
                <p class="card-header-title">
                    Ingresa tus datos para iniciar sesión
                </p>
                <a href="#" class="card-header-icon" aria-label="more options">
                    <span class="icon">
                        <i class="fas fa-angle-down" aria-hidden="true"></i>
                    </span>
                </a>
            </header>
            <div class="card-content">
                <div class="content">
                    <form method="post" action="login.php">
                        <div class="field">
                            <label class="label mt-4">Usuario</label>
                            <div class="control">
                                <input class="input" type="text" maxlength="45" id="usuario" placeholder="Usuario">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Contraseña</label>
                            <div class="control">
                                <input class="input" type="password" maxlength="45" id="contraseña" placeholder="Contraseña">
                            </div>
                        </div>
                        <button type="button" onclick="login()" class="button is-link mt-3">Iniciar sesión</button>
                        <a href="singup.php" class="button is-link mt-3 ml-3">Registrarse</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    ';
}
else{

    die('<p class="is-size-4 is-center mt-6">Usted ya tiene una sesión activa <a href="index.php">click aquí</a> para ir al sistema</p>
        </div></body></html>');

}

echo'
    <script>
        function login()
        {
            axios.post(`api/index.php/login`, {
                usuario: document.forms[0].usuario.value,
                contraseña: document.forms[0].contraseña.value
            })
            .then(resp => {
                if(resp.data.acceso == 1)
                {
                    alert(resp.data.mensaje)
                    location.href=`api/index.php/logear/${resp.data.usuario}`;
                }
                else
                {
                    alert(resp.data.mensaje)
                }
            })
            .catch(error => {
                console.log(error);
            });
        }
    </script>

    </body>
</html>
';