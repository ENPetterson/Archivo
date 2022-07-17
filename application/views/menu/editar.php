<input type="hidden" id="id" value="<?php echo $id;?>" >
<div id="ventanaMenu">
    <div id="titulo">
        Editar Menu
    </div>
    <div>
        <form id="form">
            <table>
                <tr>
                    <td style="padding-right:10px; vertical-align: middle">
                        <div id='chkPadre'>&nbsp;Padre:</div> 
                    </td>
                    <td style="padding-bottom: 10px">
                        <div id="dropDownMenu">
                            <div style="border: none;" id='treeMenu'></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px">Menu: </td>
                    <td><input type="text" id="nombre" style="width: 250px"></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; vertical-align: middle">
                        <div id='chkAccion'>&nbsp;Acci&oacute;n:</div> 
                    </td>
                    <td style="padding-bottom: 10px">
                        <input type="text" id="accion" style="width: 250px">
                    </td>
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
        
        $("#ventanaMenu").jqxWindow({showCollapseButton: false, height: 180, width: 360, theme: theme,
        resizable: false, keyboardCloseKey: -1});
    
        $("#chkPadre").jqxCheckBox({height: 25, theme: theme, checked:true });
        $("#chkAccion").jqxCheckBox({height: 25, theme: theme, checked:true });
    
        var srcMenu =
                {
                    datatype: "json",
                    datafields: [
                        { name: 'id'},
                        { name: 'padre_id'},
                        { name: 'nombre' }
                    ],
                    id: 'id',
                    url: '/menu/getAllMenues',
                    async: false
                };
        var DAMenu = new $.jqx.dataAdapter(srcMenu);
        
        DAMenu.dataBind();
        
        var records = DAMenu.getRecordsHierarchy('id', 'padre_id', 'items', [{ name: 'nombre', map: 'label'}, {name: 'id', map: 'value'}]);
        
        $("#dropDownMenu").jqxDropDownButton({ width: 150, height: 25, theme: theme });
        
        $('#treeMenu').on('select', function (event) {
            var args = event.args;
            var item = $('#treeMenu').jqxTree('getItem', args.element);
            var dropDownContent = '<div style="position: relative; margin-left: 3px; margin-top: 5px;">' + item.label + '</div>';
            $("#dropDownMenu").jqxDropDownButton('setContent', dropDownContent);
        });

        $('#treeMenu').jqxTree({ source: records, width: '300px', theme: theme });
        
        $('#chkPadre').on('checked', function (event) { 
            $("#dropDownMenu").show();
        }); 
        
        $('#chkPadre').on('unchecked', function (event) { 
            $("#dropDownMenu").hide();
        }); 
        
        $('#chkAccion').on('checked', function (event) { 
            $("#accion").show();
        }); 
        
        $('#chkAccion').on('unchecked', function (event) { 
            $("#accion").hide();
        }); 

        if ($("#id").val() == 0){
            $("#titulo").text('Nuevo Menu');
        } else {
            $("#titulo").text('Editar Menu');
            datos = {
                menu_id: $("#id").val()
            };
            $.post('/menu/getMenu', datos, function(data){
                if (data.padre_id == -1){
                    $('#chkPadre').jqxCheckBox('uncheck');
                } else {
                    var items = $('#treeMenu').jqxTree('getItems');
                    $.each(items, function(index, element){
                        if (element.value == data.padre_id){
                            $('#treeMenu').jqxTree('selectItem', element);
                        }
                    });
                }
                $("#nombre").val(data.nombre);
                if (data.accion === null){
                    $('#chkAccion').jqxCheckBox('uncheck');
                } else {
                    $("#accion").val(data.accion);
                }
            }
            , 'json');
        };
         $('#form').jqxValidator({ rules: [
                    { input: '#dropDownMenu', message: 'Debe seleccionar el menu padre', rule: function(){
                        if ($("#chkPadre").val()){
                            return $("#treeMenu").val() != null;
                        } else {
                            return true;
                        }
                    }},
                    { input: '#nombre', message: 'Debe ingresar el nombre del menu!',  rule: 'required' },
                    { input: '#accion', message: 'Debe ingresar la accion del menu!',  rule: function(){
                        if ($('#chkAccion').val()){
                            if ($.trim($('#accion').val()).length == 0){
                                return false;
                            } else {
                                return true;
                            }
                        } else {
                            return true;
                        }
                    }}
                    
                    ], 
                    theme: theme
        });
        $('#form').bind('validationSuccess', function (event) { formOK = true; });
        $('#form').bind('validationError', function (event) { formOK = false; }); 
        $('#aceptarButton').jqxButton({ theme: theme, width: '65px' });
        $('#aceptarButton').bind('click', function () {
            $('#form').jqxValidator('validate');
            if (formOK){
                $('#ventanaMenu').ajaxloader();
                var padre_id = -1;
                if ($("#chkPadre").val()){
                    padre_id = $("#treeMenu").val().id;
                }
                var accion = $("#accion").val();
                if (!$("#chkAccion").val()){
                    accion = null;
                }
                datos = {
                    id: $("#id").val(),
                    padre_id: padre_id,
                    nombre: $('#nombre').val(),
                    accion: accion
                }
                $.post('/menu/saveMenu', datos, function(data){
                    if (data.id > 0){
                        $.redirect('/menu');
                    } else {
                        new Messi('Hubo un error guardando el menu', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                        $('#ventanaMenu').ajaxloader('hide');
                    }
                }, 'json');
            }
        });                
    });
</script>