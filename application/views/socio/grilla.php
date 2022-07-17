<?php
$timestamp = time();
?>

<script type="text/javascript">
    $(document).ready(function () {
            // prepare the data
            
        var timestamp = <?= $timestamp; ?>;
        var token = '<?= md5('unique_salt' . $timestamp); ?>';    
            
        var theme = getTheme();
        var id = 0;
        var enviar = [];    


        $("#sistema").jqxMenu({width: 200, height: 25, theme: theme});
        
 

        var source = {
                datatype: "json",
                datafields: [
                { name: 'id'},
                { name: 'descripcion'},
                { name: 'anulado'},
                { name: 'tipoSocio'},
                { name: 'fechaCarga', type: 'date', format: 'yyyy-MM-dd'},
                { name: 'fhmodificacion', type: 'date', format: 'yyyy-MM-dd'}
            ],
            cache: false,
            url: '/socio/grilla',            
            type: 'post'
        };

        dataadapter = new $.jqx.dataAdapter(source);

        // initialize jqxGrid
        $("#grilla").jqxGrid(
        {		
                source: dataadapter,
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
                width: '58%',
                height: '100%',
                columns: [
                        { text: 'Id', datafield: 'id', width: 80, cellsformat: 'd', aggregates: ['count']  },
                        { text: 'Descripcion', datafield: 'descripcion', width: 140},
                        { text: 'Anulado', datafield: 'anulado', width: 140},
                        { text: 'Tipo Socio', datafield: 'tipoSocio', width: 140},
                        { text: 'Carga', datafield: 'fechaCarga', width: 140, cellsformat: 'dd/MM/yyyy'},
                        { text: 'Modificacion', datafield: 'fhmodificacion', width: 140, cellsformat: 'dd/MM/yyyy'}
                ]
        });
        $("#grilla").on("bindingcomplete", function (event){
            var localizationobj = getLocalization();
            $("#grilla").jqxGrid('localizestrings', localizationobj);
        }); 
        
        $("#nuevoButton").jqxButton({ width: '80', theme: theme });
        $("#editarButton").jqxButton({ width: '80', theme: theme, disabled: true });
        $("#borrarButton").jqxButton({ width: '80', theme: theme, disabled: true });
        
        $("#nuevoButton").click(function(){
            $.redirect('/socio/editar', {'id': 0, origen: 'socio' });
        });
        
        $("#editarButton").click(function(){
            $.redirect('/socio/editar', {'id': id, origen: 'socio' } );
        });
        
        $("#borrarButton").click(function(){
            new Messi('Desea borrar las ordenes ' + enviar.join(', ') + ' ?' , {title: 'Confirmar',titleClass: 'warning', modal: true,
                buttons: [{id: 0, label: 'Si', val: 's'}, {id: 1, label: 'No', val: 'n'}], callback: function(val) { 
                    if (val == 's'){
                        datos = {
                            ordenes: enviar
                        };
                        $.post('/socio/delOrden', datos, function(data){
                            new Messi(data.resultado, {title: 'Mensaje', modal: true,
                                buttons: [{id: 0, label: 'Cerrar', val: 'X'}], titleClass: 'error'});
                            $('#grilla').jqxGrid('updatebounddata');
                            $('#editarButton').jqxButton({disabled: true });
                            $('#borrarButton').jqxButton({disabled: true });
                            $('#grilla').jqxGrid('clearselection'); 
                        }
                        , 'json');
                    } 
                }
            });
        });

        
        $('#grilla').on('rowselect rowunselect', function (event) {
            enviar = [];
            var rowindexes = $('#grilla').jqxGrid('getselectedrowindexes');
            $('#editarButton').jqxButton({disabled: true });
            $('#borrarButton').jqxButton({disabled: true });
            if (rowindexes.length > 0){
                $.each(rowindexes, function(index, value){
                    var row = $('#grilla').jqxGrid('getrowdata', value);
                    id = row.id;
                        enviar.push(id);
                });
                if (enviar.length > 0){
                    $('#borrarButton').jqxButton({disabled: false });
                    if (enviar.length == 1){
                        $('#editarButton').jqxButton({disabled: false });
                    }
                }
            }
        });
                        
    });
</script>

<br>
<div id="sistema" style='float: left; vertical-align: text-bottom; text-align: center;'><ul>Socios</ul></div>
<br>
<br>
<div id="grilla"></div>
<div id="botonera">
    <table boder="0" cellpadding="2" cellspacing="2">
        <tr>
            <td><input type="button" value="Nuevo" id="nuevoButton"></td>
            <td><input type="button" value="Editar" id="editarButton"></td>
            <td><input type="button" value="Borrar" id="borrarButton"></td>
        </tr>
    </table>
</div>