<?php
    require_once 'functions.php';

    echo'
    <meta http-equiv="Refresh" content="0;url=login.php">
    ';

    if (isset($_SESSION['user']))
    {
        destroySession();
    }
    else
    {

    }

    echo'

    </body>

    </html>
    ';