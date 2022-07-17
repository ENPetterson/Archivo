<?php
$timestamp = time();
?>
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



            
        var timestamp = <?= $timestamp; ?>;
        var token = '<?= md5('unique_salt' . $timestamp); ?>';    
            
        var theme = getTheme();
        var id = 0;
        var enviar = [];
        var cierre_id = 0;
        
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

        $("#sistema").jqxMenu({width: '15%', height: '15%', theme: theme});
        
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
                { name: 'fhmodificacion', type: 'date', format: 'yyyy-MM-dd'},
                { name: 'observaciones'},
                { name: 'descripcion'}
            ],
            cache: false,
            url: '/registro/grilla',
            data: {cierre_id: cierre_id},
            type: 'post'
        };

        dataadapter = new $.jqx.dataAdapter(source);

        // initialize jqxGrid
        $("#grilla").jqxGrid(
        {		
                source: dataadapter,
                altrows: true,
                theme: theme,
                filterable: true,
                filtermode: 'excel',
                sortable: true,
                autoheight: false,
                pageable: false,
                virtualmode: false,
                selectionmode: 'checkbox',
                columnsresize: true,
                showstatusbar: true,
                statusbarheight: 25,
                showaggregates: true,
                width: '95%',
                height: 600,
                columns: [
                        { text: 'Id', datafield: 'id', width: 40, cellsformat: 'd', aggregates: ['count']  },
                        { text: 'Nro Socio', datafield: 'numeroSocio', width: 70},
                        { text: 'Descripcion Socio', datafield: 'descripcionSocio', width: 100},
                        { text: 'Nro Producto', datafield: 'numeroProducto', width: 70},
                        { text: 'Descripcion Producto', datafield: 'descripcionProducto', width: 90},
                        { text: 'Cant.', datafield: 'cantidad', width: 60, cellsformat: 'd', aggregates: ['sum']},
                        { text: 'Precio', datafield: 'precio', width: 70},
                        { text: 'Estado', datafield: 'estado', width: 100},
                        { text: 'estado_id', datafield: 'estado_id', width: 0, hidden: true},
                        { text: 'Carga', datafield: 'fechaCarga', width: 110, cellsformat: 'dd/MM/yyyy'},
                        { text: 'Modificacion', datafield: 'fhmodificacion', width: 110, cellsformat: 'dd/MM/yyyy'},
                        { text: 'Observaciones', datafield: 'observaciones', width: 140},
                        { text: 'Descripcion', datafield: 'descripcion', width: 150}
                ]
        });
        $("#grilla").on("bindingcomplete", function (event){
            var localizationobj = getLocalization();
            $("#grilla").jqxGrid('localizestrings', localizationobj);
        }); 


                //$("table.ui-jqgrid-htable", $("#grilla")).css ("height", 90);

        
        $("#nuevoButton").jqxButton({ width: '80', theme: theme });
        $("#editarButton").jqxButton({ width: '80', theme: theme, disabled: true });
        $("#borrarButton").jqxButton({ width: '80', theme: theme, disabled: true });
        $("#enviarButton").jqxButton({ width: '160', theme: theme, disabled: true });
        
        $("#nuevoButton").click(function(){
            $.redirect('/registro/editar', {'id': 0, origen: 'registro', cierre: $("#cierre").jqxDropDownList('getSelectedItem').value});
        });
        
        $("#editarButton").click(function(){
            $.redirect('/registro/editar', {'id': id, origen: 'registro', cierre: $("#cierre").jqxDropDownList('getSelectedItem').value});
        });
        
        $("#borrarButton").click(function(){
            new Messi('Desea borrar las ordenes ' + enviar.join(', ') + ' ?' , {title: 'Confirmar',titleClass: 'warning', modal: true,
                buttons: [{id: 0, label: 'Si', val: 's'}, {id: 1, label: 'No', val: 'n'}], callback: function(val) { 
                    if (val == 's'){
                        datos = {
                            ordenes: enviar
                        };
                        $.post('/registro/delOrden', datos, function(data){
                            new Messi(data.resultado, {title: 'Mensaje', modal: true,
                                buttons: [{id: 0, label: 'Cerrar', val: 'X'}], titleClass: 'error'});
                            $('#grilla').jqxGrid('updatebounddata');
                            $('#editarButton').jqxButton({disabled: true });
                            $('#borrarButton').jqxButton({disabled: true });
                            $('#enviarButton').jqxButton({disabled: true });
                            $('#grilla').jqxGrid('clearselection'); 
                        }
                        , 'json');
                    } 
                }
            });
        });
        
        $("#enviarButton").click(function(){
            
            datos = {
                cierre: $("#cierre").val()
            };  
            
            $.post('/registro/comprobarEstadoCierre', datos, function(data){
                if(data == 'true'){
                    var titleClass;
                    titleClass = 'success';
                    new Messi('En mantenimiento', {title: 'Mensaje', modal: true,
                    buttons: [{id: 0, label: 'Cerrar', val: 'X'}], titleClass: titleClass});
                }else{
                    new Messi('Desea enviar los registros seleccionados?' , {title: 'Confirmar',titleClass: 'warning', modal: true,
                        buttons: [{id: 0, label: 'Si', val: 's'}, {id: 1, label: 'No', val: 'n'}], callback: function(val) { 
                            if (val == 's'){
                                datos = {
                                    ordenes: enviar
                                };
                                $.post('/registro/enviarOrdenes', datos, function(data){
                                    var titleClass;
                                    if (data.exito == 0){
                                        titleClass = 'error';
                                    } else {
                                        titleClass = 'success';
                                    }
                                    new Messi(data.resultado, {title: 'Mensaje', modal: true,
                                        buttons: [{id: 0, label: 'Cerrar', val: 'X'}], titleClass: titleClass});
                                    $('#grilla').jqxGrid('updatebounddata');
                                    $('#editarButton').jqxButton({disabled: true });
                                    $('#borrarButton').jqxButton({disabled: true });
                                    $('#enviarButton').jqxButton({disabled: true });
                                    $('#grilla').jqxGrid('clearselection');
                                }
                                , 'json');
                            }
                        }
                    });
                }
            }
            , 'json');
            
            
            
            
            /*
            
            */
    
        });
        
        
        $('#grilla').on('rowselect rowunselect', function (event) {
            enviar = [];
            var rowindexes = $('#grilla').jqxGrid('getselectedrowindexes');
            $('#editarButton').jqxButton({disabled: true });
            $('#borrarButton').jqxButton({disabled: true });
            $('#enviarButton').jqxButton({disabled: true });
            if (rowindexes.length > 0){
                $.each(rowindexes, function(index, value){
                    var row = $('#grilla').jqxGrid('getrowdata', value);
                    var estado_id = row.estado_id;
                    id = row.id;
                    if (estado_id != 1){
                        $("#grilla").jqxGrid('unselectrow', value);
                    } else {
                        enviar.push(id);
                    }
                });
                if (enviar.length > 0){
                    $('#enviarButton').jqxButton({disabled: false });
                    $('#borrarButton').jqxButton({disabled: false });
                    if (enviar.length == 1){
                        $('#editarButton').jqxButton({disabled: false });
                    }
                }
            }
        });
        
////////////////////////////////////////////////////////////////////////////////   
        $("#archivoExcel").jqxButton({ width: '300', theme: theme, disabled: false });

        $('#archivoExcel').uploadifive({
            'uploadScript': '/uploadifive.php',
            'formData': {
                'timestamp': timestamp,
                'token': token
            },
            'buttonText': 'Importar Excel...',
            'multi': false,
            'queueSizeLimit': 1,
            'uploadLimit': 0,
            'height': 20,
            'width': 200,
            'removeCompleted': true,
            'onUploadComplete': function(file) {
                $('#grilla').ajaxloader();
                $.post('/registro/grabarExcel', { file: file.name, cierre: $("#cierre").jqxDropDownList('getSelectedItem').value}, function(msg){
                    var titleClass;
                    var mensaje;
                    var title;
                    if(msg.resultado == 'OK'){
                        titleClass = 'success';
                        title = 'Correcto';
                        mensaje = 'Se han importado las ordenes';
                    } else {
                        titleClass = 'error';
                        title = 'No se importaron las ordenes';
                        mensaje = msg.mensaje;
                    }
                    $('#grilla').ajaxloader('hide');
                    new Messi(mensaje, {title: title, modal: true,
                        buttons: [{id: 0, label: 'Cerrar', val: 'X'}], titleClass: titleClass, callback: function(val) { 
                            if (val == 'X'){
                                $("#grilla").jqxGrid('updatebounddata');
                            } 
                        }
                    });                    
                }, 'json');
            }
        });
////////////////////////////////////////////////////////////////////////////////                   
    });
</script>
<div id="cierre"></div>
<br>
<div id="sistema" style='float: left; vertical-align: text-bottom; text-align: center; '><ul>Ingreso de Datos</ul></div>
<br>
<br>
</br>
<div id="lala"></div>
<div id="grilla" ></div>
<br>
<div id="botonera">
    <table boder="0" cellpadding="2" cellspacing="2">
        <tr>
            <td><input type="button" value="Nuevo" id="nuevoButton"></td>
            <td><input type="button" value="Editar" id="editarButton"></td>
            <td><input type="button" value="Borrar" id="borrarButton"></td>
            <td><input type="button" value="Enviar a Control" id="enviarButton"></td>
            <td id='archivoExcelFila'><input type="file" value="Archivo" id="archivoExcel"></td>
        </tr>
    </table>
</div>