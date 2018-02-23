/*
$(document).ready(function() {

//  $j("#fechaDesde").datepicker();
//  $j("#fechaHasta").datepicker();
  var songId;

  $("#delete-confirmation").dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    buttons: {
      "Borrar": function() {
        $.get("controllers/ajax-controller.php?delete=" + songId,function(data,status){
          $("#row" + songId).fadeOut(1000);
        })//get

        $(this).dialog("close");
      },
      "Cancelar": function() {
              $(this).dialog("close");
      }
    }//buttons
  });

  $(document).on("click",".delete",function(e){
      e.preventDefault();
  		songId= $(this).data("id");

  		$("#delete-confirmation").dialog("open");
  });

  var editParam;

  $("#edit-form-dialog").dialog({
    autoOpen: false,
    resizable: false,
    modal: true//buttons
  });


  $(document).on("click",".edit",function(e){
      e.preventDefault();

  		$("#edit-form-dialog").dialog("open");
      $("#edit-id").val($(this).data("param").split('&')[0].split('=')[1]);
      $("#edit-title").val($(this).data("param").split('&')[1].split('=')[1]);
      //$("#edit-artist_name").val(param.artist_name)
      $("#edit-duration").val($(this).data("param").split('&')[3].split('=')[1]);
      //$("#edit-cd_cover").val(param.cd_cover)
      alert('h');
  });

  $(document).on("submit","#edit-form",function(e){
      e.preventDefault();

  		$("#edit-form-dialog").dialog("open");
      $("#edit-id").val($(this).data("param").split('&')[0].split('=')[1]);
      $("#edit-title").val($(this).data("param").split('&')[1].split('=')[1]);
      //$("#edit-artist_name").val(param.artist_name)
      $("#edit-duration").val($(this).data("param").split('&')[3].split('=')[1]);
      //$("#edit-cd_cover").val(param.cd_cover)
      alert('h');
  });

  $.get({
    url: "controllers/ajax-controller.php",
    success: function(data, status) {
      $("#mostrar-listado").html(data);
    }
  });

  $('#content').on('click', '.order-column', function(e) {
    e.preventDefault();

    sort = $(this).data('sort');
    column = $(this).attr('id').substr(6);

    if (sort == 'ASC') {
      sort = 'DESC';
    } else {
      sort = 'ASC';
    }

    datos = 'sort=' + sort + '&' + 'column=' + column;

    $.get({
      url: "controllers/ajax-controller.php",
      data: datos,
      success: function(data, status) {
        $("#mostrar-listado").html(data);
      }
    });
  });

  //Al escribir en el campo de texto para buscar
   $("#add").on('click',function(e){
      e.preventDefault();
      $("#add-form").css("display", "block");
       $.get({
         url: "controllers/ajax-new.php" ,
	       success: function(data,status){
			//vuelve a pintar el listado
		      $("#add-artist_id").html(data);
	      }//post
      });
   });//buscar

});
/*
  $("#wrapper").on('submit', '#modificador', function(e) {
    e.preventDefault();
    datos =  $("#modificador").serialize();
    var validacion = true;
     var expreg = /^\d{4}-\d{2}-\d{2}$/;

    if (!expreg.test($("#m-fechaInicio").val())) {
      $("#aviso-m-fechaInicio").html("Introduzca una fecha válida: DD/MM/AAAA");
      $("#aviso-m-fechaFin").css("display", "block");
      validacion = false;
    } else {
      $("#aviso-m-fechaFin").css("display", "none");
    }

    if (!expreg.test($("#m-fechaFin").val())) {
      $("#aviso-m-fechaFin").html("Introduzca una fecha válida: DD/MM/AAAA");
      $("#aviso-m-fechaFin").css("display", "block");
      validacion = false;
    } else {
      $("#aviso-m-fechaFin").css("display", "none");
    }


    if (validacion) {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: "tareas/ajax-actualizar.php",
        data: datos,
        success: function(response) {

          if (response.estado === "ok") {
            //$("#respuesta").css("height", "80px");
            if (response.error =! 1) {
              $("#respuesta").html(response.respuesta);
              $("#respuesta").css("display", "block");
            } else {
              $("#respuesta").css("display", "none");
            }

            $(".content").css({"-webkit-filter" : "blur(0)", "filter" : "blur(0)"});
            $(".wrapper a").css({"pointer-events" : "auto", "cursor" : "pointer"});
            $(".wrapper button").css({"disabled" : "false", "cursor" : "pointer", "pointer-events" : "auto", "tab-index" : "auto"});
            $("#modificador").css({"display" : "none"});

            $("#tabla").html(response.tabla);
          //  $("#respuesta").html(response.respuesta);
          }
        }
      });

    } else {
      $("#respuesta").html('Asegúrese de rellenar correctamente los campos del filtrado.');
      $("#respuesta").css("display", "block");
    }
  });

}*/
