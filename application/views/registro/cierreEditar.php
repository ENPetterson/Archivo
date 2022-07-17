<style>
    .tabla-formulario td {
        padding: 0.2em 0.2em 0.4em 0.2em;
    }
</style>
<input type="hidden" id="id" value="<?php echo $id;?>" >
<div id="ventanaCierre">
    <div id="titulo">
        Editar Cierre
    </div>
    <div>
        <form id="form">
            <table>
                <tr>
                    <td style="padding-left:10px; padding-bottom: 10px">Fecha y Hora: </td>
                    <td><div id="fechaHora"></div></td>
                </tr>
                
                <tr>
                    <td style="padding-left: 10px; padding-bottom: 10px">Pausar Cierre:</td>
                    <td><div id="pausarCierre"></div></td>
                </tr>
                
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 20px">
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
        
        
        $("#ventanaCierre").jqxWindow({showCollapseButton: false, height: 180, width: 315, position: {x:'41%', y:'35%'}, theme: theme,
        resizable: false, keyboardCloseKey: -1});
        $("#fechaHora").jqxDateTimeInput({ formatString: "dd/MM/yyyy HH:mm", showTimeButton: true, width: '190px', height: '25px', theme: theme });
        
        
        $("#pausarCierre").jqxCheckBox({ width: 200, height: 10, theme: theme });
        
        /*
        $('#fechaHora').on('change', function (event)        {  
            var jsDate = moment(event.args.date).add(1, 'd'); 
            var fecha = jsDate.toISOString().slice(0, 10).replace(/-/g, '');
            if ($("#instrumentoRegistro").val().trim().length == 0){
                $.post('/esco/getInstrumento', {fecha: fecha}, function(datos){
                    $("#instrumentoRegistro").val(datos.Abreviatura);
                }, 'json');
            }
        });

        */
        
        if ($("#id").val() == 0){
            $("#titulo").text('Nuevo Cierre');
        } else {
            $("#titulo").text('Editar Cierre');
            datos = {
                cierre_id: $("#id").val()
            };
            $.post('/registro/getCierre', datos, function(data){
                $("#fechaHora").val(data.fechahora);
                
                $("#pausarCierre").val(data.pausarCierre);
                
            }, 'json');
        };
        
        
        $('#form').jqxValidator({ rules: [
            //{ input: '#grillaPlazos', message: 'Debe ingresar al menos un plazo', action: 'blur', rule: function(){
            //    var rows = $("#grillaPlazos").jqxGrid('getrows');
            //    var rowcount = rows.length;
            //    return (rowcount > 0);
            //}},
//            { input: '#instrumentoRegistro', message: 'No existe un instrumento con ese nombre!',  rule: function(){
//                var resultado;
//                jQuery.ajaxSetup({async:false});
//                $.post('/esco/existeInstrumento', {instrumento: $('#instrumentoRegistro').val()}, function(data){
//                    resultado = data.existe;
//                }
//                , 'json');
//                jQuery.ajaxSetup({async:true});
//                return resultado;
//            }}
            ], 
            theme: theme
        });
        
        $('#form').bind('validationSuccess', function (event) { formOK = true; });
        $('#form').bind('validationError', function (event) { formOK = false; }); 
        
        $('#aceptarButton').jqxButton({ theme: theme, width: '65px' });
        $('#aceptarButton').bind('click', function () {
            $('#form').jqxValidator('validate');
            if (formOK){
                $('#ventanaCierre').ajaxloader();
                var fechaHora = moment($("#fechaHora").jqxDateTimeInput('val','date'));
                
                var datos = {
                    cierre_id: $("#id").val(),
                    fechahora: fechaHora.format("YYYY-MM-DD HH:mm") + ":00",
                    pausarCierre: $("#pausarCierre").val()
                };
                $.post('/registro/saveCierre', datos, function(data){
                    if (data.id > 0){
                        $.redirect('/registro/cierre');
                    } else {
                        new Messi('Hubo un error guardando el cierre', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                        $('#ventanaCierre').ajaxloader('hide');
                    }
                }, 'json');
            }
        });  
        
        
 });       
        

</script>