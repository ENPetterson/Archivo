<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $("body").data('theme', 'orange');
</script>
<script>
    $(function(){
        var theme = getTheme();
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'id' },
                { name: 'padre_id' },
                { name: 'nombre' },
                { name: 'accion'}
            ],
            id: 'id',
            url: '/menu/getMenues',
            async: false
        };

        // create data adapter.
        var dataAdapter = new $.jqx.dataAdapter(source);
        // perform Data Binding.
        dataAdapter.dataBind();
        // get the menu items. The first parameter is the item's id. The second parameter is the parent item's id. The 'items' parameter represents 
        // the sub items collection name. Each jqxTree item has a 'label' property, but in the JSON data, we have a 'text' field. The last parameter 
        // specifies the mapping between the 'text' and 'label' fields.  
        var records = dataAdapter.getRecordsHierarchy('id', 'padre_id', 'items', [{ name: 'nombre', map: 'label'}]);
        $('#menu').jqxMenu({ source: records, height: 30, theme: theme, width: '79,9%' });
        $("#menuUsuario").jqxMenu({ rtl: true, width: '20%', height: '30px', theme: theme });
        $("#menuUsuario").css('visibility', 'visible');
        $("#menu").bind('itemclick', function (event) {
            var datos = {
                menu_id: event.args.id
            }
            $.post('/menu/getMenu', datos, function(data){
                if (data.accion != null){
                    $(location).attr('href','/' + data.accion);
                }
            }, 'json');
        });
        $.post('/menu/getNombre', {}, function(datos){
            $("#usuario").text(datos.nombre);
        }, 'json');
    })   
</script>
    <div id="menuUsuario" style='float: right; visibility: hidden; margin-left: 0px;'>
        <ul>
            <li><a id="usuario" href="#">Usuario</a>
                <ul style='width: 180px;'>
                    <li><a href="/usuario/closeSession">Cerrar sesion</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div id="menu" style="margin-bottom: 15px; display: inline-block"></div>

