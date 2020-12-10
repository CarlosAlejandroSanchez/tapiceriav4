<?php
use Illuminate\Database\Capsule\Manager as DB;
require_once 'header.php';

if(isset($_POST['id_servicio']))
{
    echo'
    <body onload="cita()">
        <input class="is-hidden" id="id" value="'.$_POST['id_servicio'].'">
        <input class="is-hidden" id="fecha" value="'.$_POST['fecha'].'">
        <input class="is-hidden" id="id_usuario" value="'.$id.'">
    ';
}

$servicios = DB::table('servicios')->get();

echo'
    <div class="container is-fluid">

        <div class="columns producto">

        ';

        foreach($servicios as $j)
        {
            echo'
            <div class="column is-4">
                <br>
                <div class="card">
                    <div class="card-image">
                        <figure class="image is-4by3">
                            <img src="img/' . $j->img . '" class="card-img-top" width="286px" height="190px" alt="Upps, no se ha encontrado la imágen">
                        </figure>
                    </div>
                    <div class="card-content">
                        <div class="content">
                            <h5 class="card-title">' . $j->nombre . '</h5>
                            <p class="card-text">' . $j->descripcion . '</p>
                            <p style="color: green;">$' . $j->precio . '</p>
                            ';
                            if($loggedin){
                                echo'
                                <form method="post" action="index.php">
                                    <div class="field">
                                        <label class="label">Fecha</label>
                                        <div class="control">
                                            <input class="input is-primary" name="fecha" type="date" min="'.date("Y-m-d").'" placeholder="Primary input">
                                        </div>
                                    </div>
                                    <input class="input" type="hidden" name="id_servicio" value="'.$j->id_servicio.'">
                                    <button type="submit" class="mt-3 button is-success">Genera tu cita ahora!</button>
                                </form>
                                ';
                            }
            echo'
                        </div>
                    </div>
                </div>
            </div>
            ';
        }

        echo'

        </div>

    </div>

    <script>
        function cita()
        {
            axios.post(`api/index.php/cita`, {
                servicio: document.getElementById("id").value,
                fecha: document.getElementById("fecha").value,
                id_usuario: document.getElementById("id_usuario").value
            })
            .then(resp => {
                if(resp.data.aprobado == "aprobado")
                {
                    if(resp.data.fecha == "aprobado")
                    {
                        alert(resp.data.mensaje);
                    }
                    else
                    {
                        alert(resp.data.fecha)
                    }
                }
                else
                {
                    alert(resp.data.aprobado)
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