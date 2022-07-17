<div id="ventanaPermisosMenu">
    <div id="titulo">
        Permisos Menu
    </div>
    <div>
        <form id="form">
            <table>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px; vertical-align: middle">Grupo: </td>
                    <td><div id="cmbGrupo"></div></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; vertical-align: top">
                        Menues: 
                    </td>
                    <td style="padding-bottom: 10px">
                        <div style="border: none;" id='treeMenu'></div>
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
        
        $("#ventanaPermisosMenu").jqxWindow({showCollapseButton: false, showCloseButton: false, height: 400, 
            width: 390, theme: theme, resizable: false, keyboardCloseKey: -1});

        var srcGrupo =
                {
                    datatype: "json",
                    datafields: [
                        { name: 'id'},
                        { name: 'nombre' }
                    ],
                    id: 'id',
                    url: '/grupo/getGrupos',
                    async: false
                };
        var DAGrupo = new $.jqx.dataAdapter(srcGrupo);
        
        $("#cmbGrupo").jqxDropDownList({ selectedIndex: -1, source: DAGrupo, displayMember: "nombre", 
            valueMember: "id", width: 200, height: 25, theme: theme, placeHolder: "Elija un grupo:" });

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
        
        $('#treeMenu').jqxTree({ source: records, width: '300px', height: '300px', theme: theme, 
            checkboxes: true });
        
        $('#treeMenu').jqxTree('expandAll');
        
        $('#cmbGrupo').on('select', function (event) {
            $("#ventanaPermisosMenu").ajaxloader();
            $('#aceptarButton').jqxButton({ disabled: false });
            var args = event.args;
            var item = $('#cmbGrupo').jqxDropDownList('getItem', args.index);
            var grupo_id = item.value;
            datos = {
                id: grupo_id
            };
            $.post('/permiso/getGrupoMenu', datos, function(grupos){
                $('#treeMenu').jqxTree('uncheckAll');
                var items = $('#treeMenu').jqxTree('getItems');
                $.each(items, function(index, element){
                    if ($.inArray(element.value, grupos) != -1){
                        $('#treeMenu').jqxTree('checkItem', element);
                    }
                });
                $('#treeMenu').jqxTree('expandAll');
                $("#ventanaPermisosMenu").ajaxloader('hide');
            }, 'json');
        });
 
        
        $('#aceptarButton').jqxButton({ theme: theme, width: '65px', disabled: true });
        $('#aceptarButton').bind('click', function () {
            $("#ventanaPermisosMenu").ajaxloader();
            var items = $('#treeMenu').jqxTree('getCheckedItems');
            var menues = new Array();
            $.each(items, function(index, item){
                menues.push(item.value);
            });
            datos = {
                id: $("#cmbGrupo").jqxDropDownList('getSelectedItem').value,
                menues: menues
            }
            $.post('/permiso/saveGrupoMenu', datos, function(data){
                if(data.resultado){
                    new Messi('Se han guardado los permisos exitosamente', {autoclose: 3000, modal:true});
                } else {
                    new Messi('Hubo un error guardando los permisos', {title: 'Error', 
                        buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                }
                $('#ventanaPermisosMenu').ajaxloader('hide');
            }, 'json');
        });                
    });
</script>