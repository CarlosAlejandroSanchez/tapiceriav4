<?php
require_once 'header.php';
use Illuminate\Database\Capsule\Manager as DB;

echo'
<body>
    <div class="container is-fluid">
';

if(!$loggedin){

    echo'
        <div class="card mt-6">
            <header class="card-header">
                <p class="card-header-title">
                    Ingresa tus datos para registrarse
                </p>
                <a href="#" class="card-header-icon" aria-label="more options">
                    <span class="icon">
                        <i class="fas fa-angle-down" aria-hidden="true"></i>
                    </span>
                </a>
            </header>
            <div class="card-content">
                <div class="content">
                    <form method="post" action="singup.php">
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
                        <div class="field">
                            <label class="label">Repetir contraseña</label>
                            <div class="control">
                                <input class="input" type="password" maxlength="45" id="Rcontraseña" placeholder="Contraseña">
                            </div>
                        </div>
                        <button type="button" onclick="registrar()" class="button is-link mt-3">Registrarte</button>
                        <a href="login.php" class="button is-link mt-3 ml-3">Iniciar sesión</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    ';
}
else{

    die('<p class="is-size-4 center mt-6">Usted ya tiene una sesión activa <a href="index.php">click aquí</a> para ir al sistema</p>
        </div></body></html>');

}

echo'

    <script>
        function registrar()
        {
            axios.post(`api/index.php/registrar`, {
                usuario: document.forms[0].usuario.value,
                contraseña: document.forms[0].contraseña.value,
                Rcontraseña: document.forms[0].Rcontraseña.value,
            })
            .then(resp => {
                alert(resp.data.mensaje)
            })
            .catch(error => {
                console.log(error);
            });
        }
    </script>

    </body>
</html>
';