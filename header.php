<?php
require 'functions.php';

echo '
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a role="button" class="navbar-burger" data-target="navMenu" aria-label="menu" aria-expanded="true">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div class="navbar-menu" id="navMenu">
            <div class="navbar-start">
                <span class="navbar-item">
                    '.$userstr.'
                </span>
                <a href="index.php" class="navbar-item linknav">
                    Tienda
                </a>
                '.$linkloggedin.'
            </div>
            <span class="navbar-item is-pulled-right">
                '.$dinero.'
            </span>
        </div>
    </nav>

';