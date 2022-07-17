<input type="hidden" id="id" value="<?php echo $id;?>" >
<div id="ventanaControlador">
    <div id="titulo">
        Editar Controlador
    </div>
    <div>
        <form id="form">
            <table>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px">Controlador [/metodo]: </td>
                    <td><input type="text" id="nombre" style="width: 250px"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center">
                        <input type="button" id="aceptarButton" value="Aceptar">
                    </td>
                </tr>
            </table>
        </form>
    </div> 
</div>
<script>
    $(function(){
        var theme = getTheme();
        var formOK = false;
        
        $("#ventanaControlador").jqxWindow({showCollapseButton: false, height: 100, width: 440, theme: theme,
        resizable: false, keyboardCloseKey: -1});
        if ($("#id").val() == 0){
            $("#titulo").text('Nuevo Controlador');
        } else {
            $("#titulo").text('Editar Controlador');
            datos = {
                id: $("#id").val()
            };
            $.post('/controlador/getControlador', datos, function(data){
                $("#nombre").val(data.nombre);
            }
            , 'json');
        };
         $('#form').jqxValidator({ rules: [
                    { input: '#nombre', message: 'Debe ingresar el nombre del controlador!',  rule: 'required' },
                    { input: '#nombre', message: 'Ya existe un controladoor con ese nombre!', rule: function(){
                            datos = {
                                tabla: 'controlador',
                                campo: 'nombre',
                                valor: $('#nombre').val(),
                                id: $('#id').val()
                            };
                            var resultado;
                            jQuery.ajaxSetup({async:false});
                            $.post('/util/buscarDuplicado', datos, function(data){
                                if (data.resultado){
                                    resultado = false;
                                } else {
                                    resultado = true;
                                }
                            }
                            , 'json');
                            jQuery.ajaxSetup({async:true});
                            return resultado;
                    }}], 
                    theme: theme
        });
        $('#form').bind('validationSuccess', function (event) { formOK = true; });
        $('#form').bind('validationError', function (event) { formOK = false; }); 
        $('#aceptarButton').jqxButton({ theme: theme, width: '65px' });
        $('#aceptarButton').bind('click', function () {
            $('#form').jqxValidator('validate');
            if (formOK){
                $('#ventanaControlador').ajaxloader();
                datos = {
                    id: $("#id").val(),
                    nombre: $('#nombre').val()
                }
                $.post('/controlador/saveControlador', datos, function(data){
                    if (data.id > 0){
                        $.redirect('/controlador');
                    } else {
                        new Messi('Hubo un error guardando el controlador', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                        $('#ventanaControlador').ajaxloader('hide');
                    }
                }, 'json');
            }
        });                
    });
</script>