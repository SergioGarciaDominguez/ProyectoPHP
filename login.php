<?php include_once 'controllers/login-controller.php'; ?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <meta name="author" content="Sergio García Domínguez" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div id="wrapper">
      <form method="post">
        <div>
          <label for="user">Usuario</label>
          <input id="user" name="user" type="text" required="required" />
        </div>
        <div>
          <label for="password">Contraseña</label>
          <input id="password" name="password" type="text" required="required" />
        </div>
        <div>
          <input type="submit" value="Enviar" />
        </div>
      </form>
    </div>
  </body>
</html>
