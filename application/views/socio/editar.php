<input type="hidden" id="id" value="<?php echo $id;?>" >
<input type="hidden" id="origen" value="<?php echo $origen;?>" >
<input type="hidden" id="cierre" value="<?php echo $cierre;?>" >
<div id="ventanaSocio">
    <div id="titulo">
        Editar Orden Socio
    </div>
    <div>
        <form id="form">
            <table style="margin: 20px; padding: 5px; border-spacing: 10px; border-collapse: separate">
                <tr>
                    <td style="padding-right: 10px; padding-bottom: 15px">Descripcion:</td>
                    <td><input type="text" id="descripcion"></td>
                </tr>
                <tr>
                    <td style="padding-right: 10px; padding-bottom: 10px">Anulado:</td>
                    <td><div id="anulado"></div></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 1px">Tipo Socio: </td>
                    <td><div id="tipoSocio"></div></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding: 15px">
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
        var cierre_id = 0;

        var srcTipoSocio = [
            {id: '1', valor: 'Particular'},
            {id: '2', valor: 'Empresa'}
        ];
//        var precioMinimo = 0;
       
        
        $("#ventanaSocio").jqxWindow({showCollapseButton: false, height: 300, width: 390, position: {x:'37%', y:'30%'}, theme: theme, resizable: false, keyboardCloseKey: -1});
        
        //$("#tramo").jqxDropDownList({ width: '300px', height: '25px', source: ['No Competitiva', 'Competitiva'], theme: theme, selectedIndex: 0, disabled: false});
        
//        $("#tramo").jqxDropDownList({selectedIndex: 0 });
//        $("#tramo").jqxDropDownList({ disabled: true }); 
        
        $("#descripcion").jqxInput({ width: '190px', height: '25px', theme: theme});

        $("#anulado").jqxCheckBox({ width: '130px', height: '0px', theme: theme});

        $("#tipoSocio").jqxDropDownList({ width: '130px', height: '25px', theme: theme, source: srcTipoSocio, displayMember: 'valor', valueMember: 'valor', placeHolder: 'Elegir Tipo'});
        
    
        if ($("#id").val() == 0){
            $("#titulo").text('Nuevo Socio');
        } else {
            $("#titulo").text('Editar Socio');
            datos = {
                id: $("#id").val()
            };
            $.post('/socio/getSocio', datos, function(data){
                cierre_id = data.cierre_id;


                $("#descripcion").val(data.descripcion);
                $("#anulado").val(data.anulado);                
                $("#tipoSocio").val(data.tipoSocio);
            }
            , 'json');
        };
        
        
        
         $('#form').jqxValidator({ rules: [
                { input: '#descripcion', message: 'Debe ingresar la descripcion!',  rule: 'required' },
                //{ input: '#numComitente', message: 'Debe Seleccionar un comitente existente!', action: 'keyup, blur',  rule: function(){
                    //var result;
                    //if (!comitente){
                    //    result = false;
                    //} else {
                    //    result = true;
                    //}
                    //return result;
                //}},

            
                { input: '#tipoSocio', message: 'Seleccione el tipo de socio!', action: 'keyup, blur',  rule: function(){
                    return ($("#tipoSocio").jqxDropDownList('getSelectedIndex') != -1);

                    return result;
                }},  
           
                
//            { input: '#precio', message: 'precio incorrecto!', action: 'keyup, blur',  rule: function(){
//                var result = true;
//                var min = 10;
//
//
//                if ($("#precio").val() < min){
//                    $('#form').jqxValidator('rules')[4].message = "La precio debe ser mayor que " + min.toString() + "!";
//                    result = false;
//                }
//                return result;
//            }},
            
                
            
//                { input: '#precio', message: 'El precio debe ser mayor que cero!', action: 'keyup, blur',  rule: function(){
//                    if ($('#tramo').jqxDropDownList('getSelectedIndex') == 1 && $("#precio").val() == 0) {
//                        return false;
//                    } else {
//                        return true;
//                    }
//                }},
            
//                { input: '#precio', message: 'El precio debe expresarse como 0,XXXXXX !', action: 'keyup, blur',  rule: function(){
//                    if ($('#tramo').jqxDropDownList('getSelectedIndex') == 1  && $("#precio").val() >= 1) {
//                        return false;
//                    } else {
//                        return true;
//                    }
//                }}
            ], 
            theme: theme
        });
        $('#form').bind('validationSuccess', function (event) { formOK = true; });
        $('#form').bind('validationError', function (event) { formOK = false; }); 
        
        $('#aceptarButton').jqxButton({ theme: theme, width: '65px' });
        $('#aceptarButton').bind('click', function () {
            $('#form').jqxValidator('validate');
            if (formOK){
                $('#ventanaSocio').ajaxloader();
                datos = {
                    id: $("#id").val(),
                    descripcion: $("#descripcion").val(),
                    anulado: $("#anulado").val(),
                    tipoSocio: $("#tipoSocio").val()
                    
                };
                $.post('/socio/saveOrden', datos, function(data){
                    if (data.id > 0){
                        //if ($('#origen').val('procesar')){
                        //    $.redirect('/registro/procesar');
                        //} else {
                            $.redirect('/socio');
                        //}
                    } else {
                        new Messi('Hubo un error guardando el socio', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true, titleClass: 'error'});
                        $('#ventanaSocio').ajaxloader('hide');
                    }
                }, 'json');
            }
        });                
        
        $("#ventanaResumen").jqxWindow({autoOpen: false, height: 500, width:400, position: {x: 5, y: 50}, theme: theme });
        
        
        
        
        $("#grilla").on("bindingcomplete", function (event){
            var localizationobj = getLocalization();
            $("#grilla").jqxGrid('localizestrings', localizationobj);
            $("#numComitente").focus();
        }); 
        
    });
    
    //Aca va el codigo de la calculadora de registros
    $(function(){
        
    });
</script>