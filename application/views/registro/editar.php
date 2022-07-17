<input type="hidden" id="id" value="<?php echo $id;?>" >
<input type="hidden" id="origen" value="<?php echo $origen;?>" >
<input type="hidden" id="cierre" value="<?php echo $cierre;?>" >




<div id="ventanaRegistro">
    <div id="titulo">
        Editar Orden Registro
    </div>
    <div>
        <form id="form">
            <table style="margin: 20px; padding: 5px; border-spacing: 10px; border-collapse: separate">
                <tr>
                    <td style="padding-right:10px; padding-bottom: 5px">Nro Socio:</td>
                    <td><div id="numeroSocio"></div></td>
                </tr>
                <tr>
                    <td style="padding-right: 10px; padding-bottom: 5px">Descripcion Socio:</td>
                    <td><input type="text" id="descripcionSocio"></td>
                </tr>
                <input type="hidden" id="anuladoSocio">
                <tr>
                    <td style="padding-right:10px; padding-bottom: 5px">Nro Producto: </td>
                    <td><div id="numeroProducto" ></div></td>
                </tr>
                <tr>
                    <td style="padding-right: 10px; padding-bottom: 5px">Descripcion Producto:</td>
                    <td><input type="text" id="descripcionProducto"></td>
                </tr>
                <input type="hidden" id="anuladoProducto">
                <tr>
                    <td style="padding-right:10px; padding-bottom: 5px">Cantidad: </td>
                    <td><div id="cantidad"></div></td>
                </tr>
                <tr id="filaPrecio">
                    <td style="padding-right:10px; padding-bottom: 5px">Precio: </td>
                    <td><div id="precio" ></div></td>
                </tr>
                <tr>
                    <td style="padding-right: 10px; padding-bottom: 5px">Observaciones:</td>
                    <td><input type="text" id="observaciones"></td>
                </tr>
                <tr>
                    <td style="padding-right: 10px; padding-bottom: 5px">Descripcion:</td>
                    <td><input type="text" id="descripcion"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding: 10px">
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
        var anulado = 0;
        var socio = false;
        var producto = false;
//        var precioMinimo = 0;
       

        

        
        $("#ventanaRegistro").jqxWindow({showCollapseButton: false, height: 470, width: 410, position: {x:'37%', y:'23%'}, theme: theme, resizable: false, keyboardCloseKey: -1});
        
        //$("#tramo").jqxDropDownList({ width: '300px', height: '25px', source: ['No Competitiva', 'Competitiva'], theme: theme, selectedIndex: 0, disabled: false});
        
//        $("#tramo").jqxDropDownList({selectedIndex: 0 });
//        $("#tramo").jqxDropDownList({ disabled: true }); 
        
        $("#numeroSocio").jqxNumberInput({ width: '130px', height: '25px', decimalDigits: 0, digits: 12, groupSeparator: ' ', max: 999999999999, theme: theme});

        $("#descripcionSocio").jqxInput({ width: '130px', height: '25px', theme: theme});

        $("#anuladoSocio").jqxInput({ width: '130px', height: '25px', theme: theme });

        $("#numeroProducto").jqxNumberInput({ width: '130px', height: '25px', decimalDigits: 0, digits: 12, groupSeparator: ' ', max: 999999999999, theme: theme});

        $("#descripcionProducto").jqxInput({ width: '130px', height: '25px', theme: theme});

        $("#anuladoProducto").jqxInput({ width: '130px', height: '25px', theme: theme });

        $("#cantidad").jqxNumberInput({ width: '130px', height: '25px', decimalDigits: 0, digits: 12, groupSeparator: ' ', max: 999999999999, theme: theme});
        /*$("#precio").jqxNumberInput({ width: '110px', height: '25px', decimalDigits: 6, digits: 1, groupSeparator: ' ', max: 999999999.999999, theme: theme});*/
        $("#precio").jqxNumberInput({ width: '110px', height: '25px', decimalDigits: 2, digits: 4, groupSeparator: ' ', max: 9999.99, theme: theme});

        //$("#observaciones").jqxNumberInput({ width: '110px', height: '25px', decimalDigits: 2, digits: 4, groupSeparator: ' ', max: 9999.99, theme: theme});

        //$("#descripcion").jqxNumberInput({ width: '110px', height: '25px', decimalDigits: 2, digits: 4, groupSeparator: ' ', max: 9999.99, theme: theme});

        $("#observaciones").jqxInput({ width: '130px', height: '25px', theme: theme});

        $("#descripcion").jqxInput({ width: '130px', height: '25px', theme: theme});



        jQuery.ajaxSetup({async:false});

        $('#numeroSocio').on('valueChanged', function (event) {
            var value = $("#numeroSocio").val();

                $.post('/socio/getSocio', {id: value}, function(pSocio){
                socio = pSocio;
                if (pSocio.id > 0){

                    $("#descripcionSocio").val(pSocio.descripcion);
                    $("#anuladoSocio").val(pSocio.anulado);

                    $('#form').jqxValidator('hideHint', '#numeroSocio');
                } else {
                    $("#descripcionSocio").val(' '); 
                }

            }, 'json');

        });


        $('#numeroProducto').on('valueChanged', function (event) {
            var value = $("#numeroProducto").val();


                //MAl
                /*
                if ($('#numeroProducto').val() == 1){
                    $("#descripcionProducto").val('Cuchillito');
                    $('#form').jqxValidator('hideHint', '#numeroProducto');
                }
                else if ($('#numeroProducto').val() == 2){
                    $("#descripcionProducto").val('Tornillos');
                    $('#form').jqxValidator('hideHint', '#numeroProducto');
                }
                else if ($('#numeroProducto').val() == 3){
                    $("#descripcionProducto").val('Sierra');
                    $('#form').jqxValidator('hideHint', '#numeroProducto');
                }
                else{
                    $("#descripcionProducto").val('');
                    $('#form').jqxValidator('hideHint', '#numeroProducto');
                }
                */




                $.post('/producto/getProducto', {id: value}, function(pProducto){
                producto = pProducto;
                if (pProducto.id > 0){
                    //$("#numeroProducto").val(pProducto.id);

                    $("#descripcionProducto").val(pProducto.descripcion);
                    $("#precio").val(pProducto.precio);
                    $("#anuladoProducto").val(pProducto.anulado);

                    //anulado = pProducto.anulado;

                    $('#form').jqxValidator('hideHint', '#numeroProducto');
                    //if (!bowser.msie){
                    //    $("#ventanaResumen").jqxWindow('open');
                    //    srcOrdenes.data = {cierre_id: cierre_id, numComitente: $('#numComitente').val()};
                    //    $("#grillaOrdenes").jqxGrid('updatebounddata');
                    //}
                } else {
                    //$("#numeroProducto").val(0);
                    $("#descripcionProducto").val(' '); 
                    //$("#anuladoProducto").val(true);
                    $("#precio").val(0);

                    //$("#ventanaRegistro").jqxWindow('close');
                }





            }, 'json');




        });

        jQuery.ajaxSetup({async:true});
    
        if ($("#id").val() == 0){
            $("#titulo").text('Nueva Orden Registro');
        } else {
            $("#titulo").text('Editar Orden Registro');
            datos = {
                id: $("#id").val()
            };
            $.post('/registro/getOrden', datos, function(data){
                cierre_id = data.cierre_id;


                $("#numeroSocio").val(data.numeroSocio);
                $("#descripcionSocio").val(data.descripcionSocio);
                $("#numeroProducto").val(data.numeroProducto); 
                $("#descripcionProducto").val(data.descripcionProducto);               
                $("#cantidad").val(data.cantidad);
                $("#precio").val(data.precio);
                $("#observaciones").val(data.observaciones);
                $("#descripcion").val(data.descripcion);
            }
            , 'json');
        };
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        

            
            
            /*
                { input: '#cantidad', message: 'Cantidad incorrecta!', action: 'keyup, blur',  rule: function(){
                    var result = true;
                    var minimo;
                    var multiplo;
                    var maximo = 0;
                    if ($("#tramo").jqxDropDownList('getSelectedIndex') == 0){
                        if ($("#tipoPersona").val() == "FISICA"){
                            minimo = 10000;
                        } else {
                            minimo = 10000;
                        }
                        multiplo = 1;
                        maximo = 2000000;
                    } else {
                        minimo = 1000000;
                        multiplo = 1;
                        maximo = 0;
                    }
                    var cantidad = $("#cantidad").val();
                    $('#form').jqxValidator('hideHint', '#cantidad');
                    if (maximo > 0 && cantidad > maximo){
                        $('#form').jqxValidator('rules')[3].message = "La cantidad no puede ser mayor que " + maximo.toString() + "!";
                        result = false;
                    }
                    if (cantidad < minimo){
                        $('#form').jqxValidator('rules')[3].message = "La cantidad debe ser mayor o igual que " + minimo.toString() + "!";
                        result = false;
                    } else {
                        if (cantidad % multiplo > 0){
                            $('#form').jqxValidator('rules')[3].message = "La cantidad debe ser multiplo de " + multiplo.toString() +"!";
                            result = false;
                        }
                    }
                    return result;
                }},
                
                */
               
               
//                if ($("#precio").jqxDropDownList('getSelectedIndex') == 0){
//                        if ($('#tramo').jqxDropDownList('getSelectedIndex') == 1 && $("#precio").val() == 0){
//                            minimo = 10000;
//                        } else {
//                            minimo = 10000;
//                        }
//                        multiplo = 1;
//                        maximo = 2000000;
//                    } else {
//                        minimo = 1000000;
//                        multiplo = 1;
//                        maximo = 0;
//                    }
//                }},


                /*
                { input: '#precio', message: 'Precio minimo 953.03 para plazo 60!', action: 'keyup, blur',  rule: function(){
                    if ($('#tramo').jqxDropDownList('getSelectedIndex') == 1 && $("#plazo").val() == '60' && $("#precio").val() < 953.03 ) {
//                    if ($("#plazo").val() == '50' && $("#precio").val() < 949.82 ) {
                        return false;
                    } else {
                        return true;
                    }
                }},   
            
                { input: '#precio', message: 'Precio minimo 991.13 para plazo de 364!', action: 'keyup, blur',  rule: function(){
                    if ($('#tramo').jqxDropDownList('getSelectedIndex') == 1 && $("#plazo").val() == '364' && $("#precio").val() < 991.13 ) {
                        return false;
                    } else {
                        return true;
                    }
                }},   
                */

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
         $('#form').jqxValidator({ rules: [


                { input: '#numeroSocio', message: 'Se debe agregar un socio existente!', action: 'keyup, blur',  rule: function(){
                    if ($("#numeroSocio").val() > 7) {

                        return false;

                    } else {

                        return true;

                    }

                }},


                
                { input: '#numeroProducto', message: 'Debe Seleccionar no anulado!', action: 'keyup, blur',  rule: function(){
                    var result = true;

                    if($("#anuladoProducto").val() == false || $("#anuladoProducto").val() == "false"){

                        result = true; 

                    } else {

                        result = false;

                    }

                    return result;
                }},



                { input: '#numeroProducto', message: 'Se debe agregar un producto existente!', action: 'keyup, blur',  rule: function(){
                    if ($("#numeroProducto").val() > 10) {

                        return false;

                    } else {

                        return true;

                    }

                }}



                
            
                
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
                $('#ventanaRegistro').ajaxloader();
                datos = {
                    id: $("#id").val(),
                    numeroSocio: $("#numeroSocio").val(),
                    descripcionSocio: $("#descripcionSocio").val(),
                    numeroProducto: $("#numeroProducto").val(),
                    descripcionProducto: $("#descripcionProducto").val(),
                    cantidad: $("#cantidad").val(),
                    precio: $("#precio").val(),
                    observaciones: $("#observaciones").val(),
                    descripcion: $("#descripcion").val()
                    
                };
                $.post('/registro/saveOrden', datos, function(data){
                    if (data.id > 0){
                        //if ($('#origen').val('procesar')){
                        //    $.redirect('/registro/procesar');
                        //} else {
                            $.redirect('/registro');
                        //}
                    } else {
                        new Messi('Hubo un error guardando el registro', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true, titleClass: 'error'});
                        $('#ventanaRegistro').ajaxloader('hide');
                    }
                }, 'json');
            }
        });                
        
        $("#ventanaRegistro").jqxWindow({autoOpen: false, height: 500, width:400, position: {x: 5, y: 50}, theme: theme });
        
        
        
        
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