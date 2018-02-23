<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Principal - Canciones</title>
    <meta name="author" content="Sergio García Domínguez" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/jquery.validate.js"></script>
    <!--<script src="js/code.js"></script> !-->
  </head>
  <body>
    <header id="header">
      <div class="center">
        <img id="logo" alt="logo" src="img/logo.png" height="40" width="40" />
        <h1>Cloudmusic</h1>
        <!--<img id="dropdown-menu-btn" alt="menu" src="img/dropdown-menu.png" height="40"
            width="40" /> -->
        <nav id="menu">
          <ul>
            <li><a href="index.php?c=Cancion&m=grid">Top Canciones</a></li>
            <li><a href="index.php?c=MiCancion&m=grid">Mis canciones</a></li>
            <?php if ($obj->user()): ?>
              <?= $obj->user()->nick ?>: <a href="?c=User&m=close">Cerrar sesión</a>
            <?php else: ?>
              <li><a href="index.php?c=Login&m=show">Login</a>/<a href="index.php?c=Register&m=show">Register</a></li>
            <?php endif; ?>
          </ul>
        </nav>
        <?php if (isset($user)): ?>
          <span>Cerrar sesión</span>
        <?php endif; ?>
      </div>
    </header>
    <div id="content">
        <?php require($obj->getView()); ?>
    </div>
    <footer id="footer">
    </footer>
  </body>
</html>
