<?php
  //seguridad
	if(Session::getVariable('autentication')== sha1(Session::getVariable('username')))
		header('Location:index?c=cancion&m=grid');
?>
<main id="main" class="center">
    <form id="login" action="index?c=UsuarioController&m=add" method="post">
      <div>
        <label>Usuario: </label>
        <input id="nick" name="nick" required="required" maxlength="63" type="text" />
      </div>
      <div>
        <label>Contraseña: </label>
        <input id="password" name="password" type="text" required="required" maxlength="63" />
      </div>
      <input type="submit" value="Registrarme" />
    </form>

</main>
<script>
jQuery(document).ready(function() {

  // Init Theme Core
    $('#loginForm').submit(function() {
      //if ($('#saveUser').prop('checked')) var saveUser=1;
      //else var saveUser=0;
      $.post( "main.php", { username: $('#username').val(), password: $('#password').val(), password2: $('#password2').val()})
        .done(function( data ) {
            console.log(data);

        document.location.href="main.php?c=Clientes&m=grid";

      return false;
    });

});
  $('.checkbox').uniform();
</script>
