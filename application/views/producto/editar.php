<input type="hidden" id="id" value="<?php echo $id;?>" >
<input type="hidden" id="origen" value="<?php echo $origen;?>" >
<input type="hidden" id="cierre" value="<?php echo $cierre;?>" >
<div id="ventanaProducto">
    <div id="titulo">
        Editar Orden Producto
    </div>
    <div>
        <form id="form">
            <table style="margin: 20px; padding: 5px; border-spacing: 10px; border-collapse: separate">
                <tr>
                    <td style="padding-right: 10px; padding-bottom: 5px">Descripcion:</td>
                    <td><input type="text" id="descripcion"></td>
                </tr>
                <tr>
                    <td style="padding-right: 5px; padding-bottom: 10px">Tipo Producto: </td>
                    <td style="padding-top: 10px"><div id="cmbtipoProducto"></div></td>
                </tr>
                <tr>
                    <td style="padding-right: 10px; padding-bottom: 10px">Anulado:</td>
                    <td><div id="anulado"></div></td>
                </tr>
                <tr id="filaPrecio">
                    <td style="padding-right:10px; padding-bottom: 5px">Precio: </td>
                    <td><div id="precio"></div></td>
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
//        var precioMinimo = 0;


        
        $("#ventanaProducto").jqxWindow({showCollapseButton: false, height: 370, width: 360, position: {x:'39%', y:'28%'}, theme: theme, resizable: false, keyboardCloseKey: -1});
        
        //$("#tramo").jqxDropDownList({ width: '300px', height: '25px', source: ['No Competitiva', 'Competitiva'], theme: theme, selectedIndex: 0, disabled: false});
        
//        $("#tramo").jqxDropDownList({selectedIndex: 0 });
//        $("#tramo").jqxDropDownList({ disabled: true }); 
        
        $("#descripcion").jqxInput({ width: '150px', height: '25px', theme: theme});
        
        $("#anulado").jqxCheckBox({ width: '130px', height: '0px', theme: theme});

        $("#precio").jqxNumberInput({ width: '110px', height: '25px', decimalDigits: 2, digits: 4, groupSeparator: ' ', max: 9999.99, theme: theme});


        var srcTipoProducto =
            {
                datatype: "json",
                datafields: [
                    { name: 'id'},
                    { name: 'descripcion' }
                ],
                id: 'id',
                url: '/producto/getTipoProductos',
                async: false
            };
        var DATipoProducto = new $.jqx.dataAdapter(srcTipoProducto);
        $("#cmbtipoProducto").jqxDropDownList({ selectedIndex: -1, source: DATipoProducto, displayMember: "descripcion", valueMember: "descripcion", width: 150, height: 25, theme: theme, placeHolder: "Elija la Categoria:" });
  
    
        if ($("#id").val() == 0){
            $("#titulo").text('Nuevo Producto');
        } else {
            $("#titulo").text('Editar Producto');
            datos = {
                id: $("#id").val()
            };
            $.post('/producto/getProducto', datos, function(data){
                cierre_id = data.cierre_id;


                $("#descripcion").val(data.descripcion);
                $("#cmbtipoProducto").val(data.tipoProducto);                
                $("#anulado").val(data.anulado);
                $("#precio").val(data.precio);

            }
            , 'json');
        };
        

        
         $('#form').jqxValidator({ rules: [
                //{ input: '#numComitente', message: 'Debe Seleccionar un comitente existente!', action: 'keyup, blur',  rule: function(){
                    //var result;
                    //if (!comitente){
                    //    result = false;
                    //} else {
                    //    result = true;
                    //}
                    //return result;
                //}},

                
            
                
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
                $('#ventanaProducto').ajaxloader();
                datos = {
                    id: $("#id").val(),
                    descripcion: $("#descripcion").val(),
                    tipoProducto: $("#cmbtipoProducto").jqxDropDownList('getSelectedItem').value,
                    anulado: $("#anulado").val(),
                    precio: $("#precio").val()
                    
                };
                $.post('/producto/saveOrden', datos, function(data){
                    if (data.id > 0){
                        //if ($('#origen').val('procesar')){
                        //    $.redirect('/registro/procesar');
                        //} else {
                            $.redirect('/producto');
                        //}
                    } else {
                        new Messi('Hubo un error guardando el producto', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true, titleClass: 'error'});
                        $('#ventanaProducto').ajaxloader('hide');
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