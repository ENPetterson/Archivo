<script type="text/javascript">
    $(document).ready(function () {
            // prepare the data
        var theme = getTheme();
        var id = 0;
        var fechaCierre;
        
        $("#sistema").jqxMenu({width: 200, height: 30, theme: theme});

        var source =
        {
                datatype: "json",
                datafields: [
                { name: 'id'},
                { name: 'fechahora', type: 'date', format: 'yyyy-MM-dd HH:mm:ss'}
        ],
        cache: false,
        url: '/registro/cierreGrilla',
        filter: function()
        {
                // update the grid and send a request to the server.
                $("#grilla").jqxGrid('updatebounddata', 'filter');
        },
        sort: function()
        {
                // update the grid and send a request to the server.
                $("#grilla").jqxGrid('updatebounddata', 'sort');
        },
        root: 'Rows',
        beforeprocessing: function(data)
        {		
                if (data != null)
                {
                        source.totalrecords = data[0].TotalRows;					
                }
        }
        };		
        var dataadapter = new $.jqx.dataAdapter(source, {
                loadError: function(xhr, status, error)
                {
                        alert(xhr.responseText);
                }
        }
        );

        // initialize jqxGrid
        $("#grilla").jqxGrid(
        {		
                source: dataadapter,
                theme: theme,
                filterable: true,
                sortable: true,
                autoheight: true,
                pageable: true,
                virtualmode: true,
                width: 200,
                rendergridrows: function(obj)
                {
                        return obj.data;    
                },
                columns: [
                        { text: 'Id', datafield: 'id', width: 0, hidden: true },
                        { text: 'Fecha y Hora', datafield: 'fechahora', width: 200, cellsformat: 'dd/MM/yyyy HH:mm:ss'}
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
            $.redirect('/registro/cierreEditar', {'id': 0});
        });
        
        $("#editarButton").click(function(){
            $.redirect('/registro/cierreEditar', {'id': id});
        });
        
        $("#borrarButton").click(function(){
            new Messi('Desea borrar el cierre del ' + fechaCierre + ' ?' , {title: 'Confirmar',titleClass: 'warning', modal: true,
                buttons: [{id: 0, label: 'Si', val: 's'}, {id: 1, label: 'No', val: 'n'}], callback: function(val) { 
                    if (val == 's'){
                        datos = {
                            id: id
                        };
                        $.post('/registro/delCierre', datos, function(data){
                            new Messi(data.resultado, {title: 'Mensaje', modal: true,
                                buttons: [{id: 0, label: 'Cerrar', val: 'X'}], titleClass: 'error'});
                            $('#grilla').jqxGrid('updatebounddata');
                            $('#editarButton').jqxButton({disabled: true });
                            $('#borrarButton').jqxButton({disabled: true });
                            $('#grilla').jqxGrid({ selectedrowindex: -1}); 
                        }
                        , 'json');
                    } 
                }
            });
        });
        
        $('#grilla').on('rowselect', function (event) {
            var args = event.args; 
            var row = args.rowindex;
            if (row >= 0){
                $('#editarButton').jqxButton({disabled: false });
                $('#borrarButton').jqxButton({disabled: false });
                id = args.row.id;
                var fechaHora = moment(args.row.fechahora);
                fechaCierre =  fechaHora.format("DD/MM/YYYY HH:mm");
            }
        });
        
        
        
    });
</script>
<div>
<div id="sistema" style='float: left; vertical-align: text-bottom; text-align: center;'><ul>Grilla Cierre</ul></div>
<br>
<br>
<div id="grilla"></div>
<div>
    <table boder="0" cellpadding="2" cellspacing="2">
        <tr>
            <td><input type="button" value="Nuevo" id="nuevoButton"></td>
            <td><input type="button" value="Editar" id="editarButton"></td>
            <td><input type="button" value="Borrar" id="borrarButton"></td>
        </tr>
    </table>
</div>
</div>