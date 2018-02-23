<?php
  //seguridad
	if(Session::getVariable('autentication')== sha1(Session::getVariable('username')))
		header('Location:index?c=cancion&m=grid');
?>
<main id="register" class="center">
    <form id="edit-form" action="index?c=UsuarioController&m=add" method="post">
      <div>
        <label>Usuario: </label>
        <input id="nick" name="nick" required="required" maxlength="63" type="text" />
      </div>
      <div>
        <label>Contraseña: </label>
        <input id="password" name="password" type="text" required="required" maxlength="63" />
      </div>
			<div>
        <label>Email: </label>
        <input id="email" name="email" required="required" maxlength="320" type="text" />
      </div>
      <input type="submit" value="Registrarme" />
    </form>
</main>
