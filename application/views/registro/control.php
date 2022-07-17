
<style>
    .jqx-grid-cell-alt{
        background: '#ffc683';
    }

    .jqx-widget-header {
        white-space:normal !important;
        /*height:50px !important;*/
        /*padding:2px;*/
        word-wrap: break-word; /* IE 5.5+ and CSS3 */
        white-space: pre-wrap; /* CSS3 */
        white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
        white-space: -pre-wrap; /* Opera 4-6 */
        white-space: -o-pre-wrap; /* Opera 7 */
        overflow: hidden;
        height: 50px !important;
        vertical-align: middle;
        padding-top: 3px*;
        padding-bottom: 3px;

    }

</style> 
<script type="text/javascript">
    $(document).ready(function () {
            // prepare the data
        var theme = getTheme();
        var id = 0;
        var enviar = [];
        var cierre_id = 0;
        var cierreFecha;
        
        var srcCierre = {
            datatype: "json",
            datafields: [
                { name: 'id'},
                { name: 'fechahora' }
            ],
            id: 'id',
            url: '/registro/getCierres',
            async: false
        };
        var DACierre = new $.jqx.dataAdapter(srcCierre);

        $("#sistema").jqxMenu({width: 200, height: 25, theme: theme});
        
        $("#cierre").on('bindingComplete', function(event){
            $.post('/registro/getCierreActual', function(cierre){
                if (!cierre.cerrado){
                    $("#cierre").jqxDropDownList('val', cierre.id);
                }
            },'json');
        });
        
        $("#cierre").jqxDropDownList({ selectedIndex: -1, source: DACierre, displayMember: "fechahora", 
            valueMember: "id", width: 200, height: 25, theme: theme, placeHolder: "Elija el cierre:", renderer: function (index, label, value){
                return moment(label).format('DD/MM/YYYY HH:mm');
            }  
        });
        
        $('#cierre').on('change', function (event)        {  
            source.data = {cierre_id: event.args.item.value};
            $("#grilla").jqxGrid('updatebounddata');
            $("#excelButton").jqxButton({disabled: false });
            cierreFecha = event.args.item.label;
        });

        var source = {
            datatype: "json",
            datafields: [
                { name: 'id'},
                { name: 'numeroSocio', type: 'number'},
                { name: 'descripcionSocio'},
                { name: 'numeroProducto', type: 'number'},
                { name: 'descripcionProducto'},
                { name: 'cantidad', type: 'number'},
                { name: 'precio', type: 'float'},
                { name: 'estado'},
                { name: 'estado_id'},
                { name: 'fechaCarga', type: 'date', format: 'yyyy-MM-dd'},
                { name: 'fhmodificacion', type: 'date', format: ' yyyy-MM-dd'},
                { name: 'observaciones'},
                { name: 'descripcion'}
            ],
            cache: false,
            url: '/registro/controlGrilla',
            data: {cierre_id: cierre_id},
            type: 'post'
        };
        
        var dataadapter = new $.jqx.dataAdapter(source);

        // initialize jqxGrid
        $("#grilla").jqxGrid(
        {		
                source: dataadapter,
                altrows: true,
                theme: theme,
                filterable: true,
                sortable: true,
                pageable: false,
                virtualmode: false,
                selectionmode: 'checkbox',
                columnsresize: true,
                showstatusbar: true,
                statusbarheight: 25,
                showaggregates: true,
                width: "96%",
                height: 420,
                columns: [
                        { text: 'Id', datafield: 'id', width: 40, cellsformat: 'd', aggregates: ['count']  },
                        { text: 'Nro Socio', datafield: 'numeroSocio', width: 70},
                        { text: 'Descripcion Socio', datafield: 'descripcionSocio', width: 100},
                        { text: 'Nro Producto', datafield: 'numeroProducto', width: 70},
                        { text: 'Descripcion Producto', datafield: 'descripcionProducto', width: 100},
                        { text: 'Cantidad', datafield: 'cantidad', width: 100, cellsformat: 'd', aggregates: ['sum']},
                        { text: 'Precio', datafield: 'precio', width: 70, cellsformat: 'd'},
                        { text: 'Estado', datafield: 'estado', width: 100},
                        { text: 'estado_id', datafield: 'estado_id', width: 0, hidden: true},
                        { text: 'Carga', datafield: 'fechaCarga', width: 100, cellsformat: 'dd/MM/yyyy'},
                        { text: 'Modificacion', datafield: 'fhmodificacion', width: 100, cellsformat: 'dd/MM/yyyy'},
                        { text: 'Observaciones', datafield: 'observaciones', width: 100},
                        { text: 'Descripcion', datafield: 'descripcion', width: 100}
                ]
        });
        $("#grilla").on("bindingcomplete", function (event){
            var localizationobj = getLocalization();
            $("#grilla").jqxGrid('localizestrings', localizationobj);
        }); 
        
        $("#excelButton").jqxButton({ width: '160', theme: theme, disabled: true });
        
        $("#excelButton").click(function(){
            grid2excel('#grilla', 'Operaciones Cierre - ' + cierreFecha, false);
        });
        
        
        $("#editarButton").jqxButton({ width: '160', theme: theme, disabled: true });
        $("#anularButton").jqxButton({ width: '160', theme: theme, disabled: true });


        
        $("#anularButton").click(function(){
            new Messi('Desea anular el/los registros ' + enviar.join(', ') + ' ?' , {title: 'Confirmar',titleClass: 'warning', modal: true,
                buttons: [{id: 0, label: 'Si', val: 's'}, {id: 1, label: 'No', val: 'n'}], callback: function(val) { 
                    if (val == 's'){
                        $("#grilla").ajaxloader();
                        datos = {
                            ordenes: enviar
                        };
                        $.post('/registro/anularOrdenes', datos, function(data){
                            new Messi(data.resultado, {title: 'Mensaje', modal: true,
                                buttons: [{id: 0, label: 'Cerrar', val: 'X'}], titleClass: 'error'});
                            $('#grilla').jqxGrid('updatebounddata');
                            $('#grilla').jqxGrid('clearselection');
                            $("#grilla").ajaxloader('hide');
                        }
                        , 'json');
                    } 
                }
            });
        });
        
        $("#editarButton").click(function(){
            $.redirect('/registro/editar', {'id': id, origen: 'control'});
        });
        

        
        
        $('#grilla').on('rowselect rowunselect', function (event) {
            enviar = [];
            var rowindexes = $('#grilla').jqxGrid('getselectedrowindexes');
            $('#editarButton').jqxButton({disabled: true });
            $('#anularButton').jqxButton({disabled: true });
            if (rowindexes.length > 0){
                $.each(rowindexes, function(index, value){
                    var row = $('#grilla').jqxGrid('getrowdata', value);
                    var estado_id = row.estado_id;
                    id = row.id;
                    if (estado_id != 2){
                        $("#grilla").jqxGrid('unselectrow', value);
                    } else {
                        enviar.push(id);
                    }
                });
                if (enviar.length > 0){
                    if (enviar.length == 1){
                        $('#editarButton').jqxButton({disabled: false });
                    }
                    $('#anularButton').jqxButton({disabled: false });
                }
            }
            //srcGrillaResumen.data = {ordenes: enviar};
        });
        
        
        

    });
</script>
<div id="cierre"></div>
<br>
<div id="sistema" style='float: left; vertical-align: text-bottom; text-align: left;'><ul>Control</ul></div>
<br>
<br>
<div id="grilla"></div>
<div id="botonera">
    <table boder="0" cellpadding="2" cellspacing="2">
        <tr>
            <td><input type="button" value="Anular" id="anularButton"></td>
            <td><input type="button" value="Editar" id="editarButton"></td>
            <td><input type="button" value="Generar Excel" id="excelButton"></td>
        </tr>
    </table>
</div>
