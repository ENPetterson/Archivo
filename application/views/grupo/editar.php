<input type="hidden" id="id" value="<?php echo $id;?>" >
<div id="ventanaGrupo">
    <div id="titulo">
        Editar Grupo
    </div>
    <div>
        <form id="form">
            <table>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px">Grupo: </td>
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
        
        $("#ventanaGrupo").jqxWindow({showCollapseButton: false, height: 100, width: 350, theme: theme,
        resizable: false, keyboardCloseKey: -1});
        if ($("#id").val() == 0){
            $("#titulo").text('Nuevo Grupo');
        } else {
            $("#titulo").text('Editar Grupo');
            datos = {
                id: $("#id").val()
            };
            $.post('/grupo/getGrupo', datos, function(data){
                $("#nombre").val(data.nombre);
            }
            , 'json');
        };
         $('#form').jqxValidator({ rules: [
                    { input: '#nombre', message: 'Debe ingresar el nombre del grupo!',  rule: 'required' },
                    { input: '#nombre', message: 'Ya existe un grupo con ese nombre!',  rule: function(){
                            datos = {
                                tabla: 'grupo',
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
                $('#ventanaGrupo').ajaxloader();
                datos = {
                    id: $("#id").val(),
                    nombre: $('#nombre').val()
                }
                $.post('/grupo/saveGrupo', datos, function(data){
                    if (data.id > 0){
                        $.redirect('/grupo');
                    } else {
                        new Messi('Hubo un error guardando el grupo', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                        $('#ventanaGrupo').ajaxloader('hide');
                    }
                }, 'json');
            }
        });                
    });
</script>