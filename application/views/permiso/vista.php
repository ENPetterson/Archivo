<div id="ventanaPermisosVista">
    <div id="titulo">
        Permisos Vistas
    </div>
    <div>
        <form id="form">
            <table>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px; vertical-align: middle">Grupo: </td>
                    <td><div id="cmbGrupo"></div></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px; vertical-align: middle">Vista: </td>
                    <td><div id="cmbVista"></div></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-right:10px; vertical-align: top">
                        Permisos <br>
                        <div style="float: left;" id="grdPermisos">
                        </div>
                        <div style="margin-left: 10px; float: left; display: inline;">
                            <input id="addPermiso" type="button" value="Agregar" />
                            <input id="delPermiso" type="button" value="Borrar" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 10px">
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
        
        $("#addPermiso").jqxButton({ theme: theme });
        $("#delPermiso").jqxButton({ theme: theme });
        
        $("#ventanaPermisosVista").jqxWindow({showCollapseButton: false, showCloseButton: false, height: 400, 
            width: 320, theme: theme, resizable: false, keyboardCloseKey: -1});

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

        var srcVista =
                {
                    datatype: "json",
                    datafields: [
                        { name: 'id'},
                        { name: 'nombre' }
                    ],
                    id: 'id',
                    url: '/vista/getVistas',
                    async: false
                };
        var DAVista = new $.jqx.dataAdapter(srcVista);
        
        $("#cmbVista").jqxDropDownList({ selectedIndex: -1, source: DAVista, displayMember: "nombre", 
            valueMember: "id", width: 200, height: 25, theme: theme, placeHolder: "Elija la vista:", disabled: true });

        $('#cmbGrupo').on('select', function (event) {
            $("#cmbVista").jqxDropDownList({ disabled: false});
        });

        $('#cmbVista').on('select', function (event) {
            $("#ventanaPermisosVista").ajaxloader();
            $('#aceptarButton').jqxButton({ disabled: false });
            var args = event.args;
            var item = $('#cmbVista').jqxDropDownList('getItem', args.index);
            var vista_id = item.value;
            
            var srcPermisos =
                    {
                        datatype: "json",
                        datafields: [
                            { name: 'tipo'},
                            { name: 'elemento' }
                        ],
                        data: {
                            grupo_id: $("#cmbGrupo").jqxDropDownList('getSelectedItem').value,
                            vista_id: vista_id
                        },
                        type: 'post',
                        url: '/permiso/getPermisos',
                        async: false
                    };
            var DAPermisos = new $.jqx.dataAdapter(srcPermisos);

            $("#grdPermisos").jqxGrid({
                source: DAPermisos,
                editable: true,
                theme: theme,
                selectionmode: 'singlecell',
                width: 290,
                height: 200,
                autoheight: false,
                columns: [
                    { text: 'Tipo', columntype: 'textbox', datafield: 'tipo', width: 40, 
                        validation: function(cell,value) {
                            var validos = ['H', 'D'];
                            if ($.inArray(value, validos) == -1 ){
                                return { result: false, message: "El tipo debe ser H o D" };
                            }
                            return true;
                        }},
                    { text: 'Elemento', columntype: 'textbox', datafield: 'elemento', width: 200,
                        validation: function (cell, value) {
                            if ($.trim(value) == "") {
                                return { result: false, message: "Debe ingresar el elemento" };
                            }
                            return true;
                        }
                    }
                ]
            }); 
            $('#grdPermisos').jqxGrid('updatebounddata');
            $("#ventanaPermisosVista").ajaxloader('hide');
        }); 
        
        $('#aceptarButton').jqxButton({ theme: theme, width: '65px', disabled: true });
        $('#aceptarButton').bind('click', function () {
            $("#ventanaPermisosVista").ajaxloader();
            var permisos = new Array;
            var rows = $('#grdPermisos').jqxGrid('getrows');
            $.each(rows, function(index, element){
                permisos.push({tipo: element.tipo, elemento: element.elemento});
            });
            var datos = {
                grupo_id: $("#cmbGrupo").jqxDropDownList('getSelectedItem').value,
                vista_id: $("#cmbVista").jqxDropDownList('getSelectedItem').value,
                permisos: permisos
            }
            $.post('/permiso/savePermiso', datos, function(data){
                if(data.resultado){
                    new Messi('Se han guardado los permisos exitosamente', {autoclose: 3000, modal:true});
                } else {
                    new Messi('Hubo un error guardando los permisos', {title: 'Error', 
                        buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                }
            }, 'json');
            $("#ventanaPermisosVista").ajaxloader('hide');
        });  
        
        $("#addPermiso").bind('click', function () {
            var datarow = {
                tipo: 'H',
                elemento: ''
            };
            $("#grdPermisos").jqxGrid('addrow', null, datarow);
        });
        
        $("#delPermiso").bind('click', function () {
            var celda = $('#grdPermisos').jqxGrid('getselectedcell');
            var selectedrowindex = celda.rowindex;
            var rowscount = $("#grdPermisos").jqxGrid('getdatainformation').rowscount;
            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                var id = $("#grdPermisos").jqxGrid('getrowid', selectedrowindex);
                $("#grdPermisos").jqxGrid('deleterow', id);
            }
        });


    });
</script>