<div id="ventanaPermisosControlador">
    <div id="titulo">
        Permisos Controlador
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
                        Controladores: 
                    </td>
                    <td style="padding-bottom: 10px">
                        <div style="border: none;" id='controladores'></div>
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
        
        $("#ventanaPermisosControlador").jqxWindow({showCollapseButton: false, showCloseButton: false, height: 400, 
            width: 430, theme: theme, resizable: false, keyboardCloseKey: -1});

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

        var srcControlador =
                {
                    datatype: "json",
                    datafields: [
                        { name: 'id'},
                        { name: 'nombre' }
                    ],
                    id: 'id',
                    url: '/controlador/getAllControladores',
                    async: false
                };
        var DAControlador = new $.jqx.dataAdapter(srcControlador);
        
        $("#controladores").jqxListBox({ source: DAControlador, displayMember: "nombre", valueMember: "id", 
            checkboxes: true, width: 300, height: 300, theme: theme });
        
        $('#cmbGrupo').on('select', function (event) {
            $("#ventanaPermisosControlador").ajaxloader();
            $('#aceptarButton').jqxButton({ disabled: false });
            var args = event.args;
            var item = $('#cmbGrupo').jqxDropDownList('getItem', args.index);
            var grupo_id = item.value;
            datos = {
                id: grupo_id
            };
            $.post('/permiso/getGrupoControlador', datos, function(controlador){
                $("#controladores").jqxListBox('uncheckAll'); 
                $.each(controlador, function(key, controlador_id){
                    var item = $("#controladores").jqxListBox('getItemByValue', controlador_id);
                    $("#controladores").jqxListBox('checkItem', item ); 
                })
                $("#ventanaPermisosControlador").ajaxloader('hide');
            }, 'json');
        });
 
        
        $('#aceptarButton').jqxButton({ theme: theme, width: '65px', disabled: true });
        $('#aceptarButton').bind('click', function () {
            $("#ventanaPermisosControlador").ajaxloader();
            var items = $('#controladores').jqxListBox('getCheckedItems');
            var controladores = new Array();
                $.each(items, function (key, value){
                    controladores.push(value.value);
                });
            datos = {
                id: $("#cmbGrupo").jqxDropDownList('getSelectedItem').value,
                controladores: controladores
            }
            $.post('/permiso/saveGrupoControlador', datos, function(data){
                if(data.resultado){
                    new Messi('Se han guardado los permisos exitosamente', {autoclose: 3000, modal:true});
                } else {
                    new Messi('Hubo un error guardando los permisos', {title: 'Error', 
                        buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                }
                $('#ventanaPermisosControlador').ajaxloader('hide');
            }, 'json');
        });                
    });
</script>