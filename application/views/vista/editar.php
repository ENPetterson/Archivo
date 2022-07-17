<input type="hidden" id="id" value="<?php echo $id;?>" >
<div id="ventanaVista">
    <div id="titulo">
        Editar Vista
    </div>
    <div>
        <form id="form">
            <table>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px">Vista: </td>
                    <td><input type="text" id="nombre" style="width: 200px"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center">
                        <div id="divAceptar">
                            <input type="button" id="aceptarButton" value="Aceptar">
                        </div>
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
        
        $("#ventanaVista").jqxWindow({showCollapseButton: false, showCloseButton:false, height: 100, 
            width: 300, theme: theme, resizable: false, keyboardCloseKey: -1});
        if ($("#id").val() == 0){
            $("#titulo").text('Nueva Vista');
        } else {
            $("#titulo").text('Editar Vista');
            datos = {
                id: $("#id").val()
            };
            $.post('/vista/getVista', datos, function(data){
                $("#nombre").val(data.nombre);
            }
            , 'json');
        };
         $('#form').jqxValidator({ rules: [
                    { input: '#nombre', message: 'Debe ingresar el nombre de la vista!',  rule: 'required' },
                    { input: '#nombre', message: 'Ya existe una vista con ese nombre!',  rule: function(){
                            datos = {
                                tabla: 'vista',
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
                $('#ventanaVista').ajaxloader();
                datos = {
                    id: $("#id").val(),
                    nombre: $('#nombre').val()
                }
                $.post('/vista/saveVista', datos, function(data){
                    if (data.id > 0){
                        $.redirect('/vista');
                    } else {
                        new Messi('Hubo un error guardando la vista', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                        $('#ventanaVista').ajaxloader('hide');
                    }
                }, 'json');
            }
        });                
    });
</script>