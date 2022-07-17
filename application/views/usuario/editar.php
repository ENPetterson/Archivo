<input type="hidden" id="id" value="<?php echo $id;?>" >
<div id="ventanaUsuario">
    <div id="titulo">
        Editar Usuario
    </div>
    <div>
        <form id="form">
            <table>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px">Usuario: </td>
                    <td><input type="text" id="nombreUsuario" style="width: 250px" class="text-input"></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px; vertical-align: middle">Dominio: </td>
                    <td><div id="dominio"></div></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 10px; text-align: right">Nombre:</td>
                    <td><input type="text" id="nombre" style="width: 250px" class="text-input"></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 20px; text-align: right">Apellido:</td>
                    <td><input type="text" id="apellido" style="width: 250px" class="text-input"></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 20px; text-align: right">Email:</td>
                    <td><input type="text" id="email" style="width: 250px" class="text-input"></td>
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-bottom: 20px; text-align: right; vertical-align: top">Grupos:</td>
                    <td style="padding-bottom: 10px"><div id="grupos"></div></td>
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
        
        if (theme.length > 0) {
            $('.text-input').addClass('jqx-input-' + theme);
            $('.text-input').addClass('jqx-widget-content-' + theme);
            $('.text-input').addClass('jqx-rc-all-' + theme);
        }
        
        var url = "/grupo/getGrupos";

        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'id'},
                { name: 'nombre' }
            ],
            id: 'id',
            url: url
        };
        var dataAdapter = new $.jqx.dataAdapter(source);

        // Create a jqxListBox
        $("#grupos").jqxListBox({ source: dataAdapter, displayMember: "nombre", valueMember: "id", 
            checkboxes: true, width: 250, height: 100, theme: theme });

        $("#ventanaUsuario").jqxWindow({showCollapseButton: false, height: 380, width: 340, theme: theme,
        resizable: false, keyboardCloseKey: -1});
        if ($("#id").val() == 0){
            $("#titulo").text('Nuevo Usuario');
        } else {
            $("#titulo").text('Editar Usuario');
            datos = {
                id: $("#id").val()
            };
            $.post('/usuario/getUsuario', datos, function(data){
                $("#nombreUsuario").val(data.nombreUsuario);
                $("#dominio").val(data.dominio);
                $("#nombre").val(data.nombre);
                $("#apellido").val(data.apellido);
                $("#email").val(data.email);
                $.each(data.grupos, function(key, grupo_id){
                    var item = $("#grupos").jqxListBox('getItemByValue', grupo_id);
                    $("#grupos").jqxListBox('checkItem', item ); 
                })
            }
            , 'json');
        };
         $('#form').jqxValidator({ rules: [
                    { input: '#nombreUsuario', message: 'Debe ingresar el nombre de usuario!',  rule: 'required' },
                    { input: '#nombreUsuario', message: 'Ya existe un usuario con ese nombre de usuario!',  rule: function(){
                            datos = {
                                tabla: 'usuario',
                                campo: 'nombreUsuario',
                                valor: $('#nombreUsuario').val(),
                                id: $('#id').val()
                            };
                            var resultado;
                            jQuery.ajaxSetup({async:false});
                            $.post('/util/buscarDuplicado', datos, function(data){
                                if (data.resultado){
                                    resultado = false;
                                } else {
                                    resultado = true;
                                }
                            }
                            , 'json');
                            jQuery.ajaxSetup({async:true});
                            return resultado;
                    }},
                    { input: '#nombre', message: 'Debe ingresar el nombre!', action: 'keyup', rule: 'required' },
                    { input: '#apellido', message: 'Debe ingresar el apellido!', action: 'keyup', rule: 'required' },
                    { input: '#email', message: 'Debe ingresar el email!', action: 'keyup', rule: 'required' },
                    { input: '#email', message: 'El e-mail no es valido!', action: 'keyup', rule: 'email' }
                    ], 
                    theme: theme
        });
        $('#form').bind('validationSuccess', function (event) { formOK = true; });
        $('#form').bind('validationError', function (event) { formOK = false; }); 

        var sourceDominios = [<?php echo DOMINIOS;?>];
        $("#dominio").jqxDropDownList({ source: sourceDominios, selectedIndex: 0, width: '250', height: '20px', theme: theme});

        $('#aceptarButton').jqxButton({ theme: theme, width: '65px' });
        $('#aceptarButton').bind('click', function () {
            $('#form').jqxValidator('validate');
            if (formOK){
                $('#ventanaUsuario').ajaxloader();
                var items = $("#grupos").jqxListBox('getCheckedItems'); 
                var grupos = new Array();
                $.each(items, function (key, value){
                    grupos.push(value.value);
                });
                datos = {
                    id: $("#id").val(),
                    nombreUsuario: $('#nombreUsuario').val(),
                    dominio: $('#dominio').val(),
                    nombre: $('#nombre').val(),
                    apellido: $('#apellido').val(),
                    email: $("#email").val(),
                    grupos: grupos
                }
                $.post('/usuarioPublico/saveUsuario', datos, function(data){
                    if (data.id > 0){
                        $.redirect('/usuarioPublico');
                    } else {
                        new Messi('Hubo un error guardando el usuario', {title: 'Error', 
                            buttons: [{id: 0, label: 'Cerrar', val: 'X'}], modal:true});
                        $('#ventanaUsuario').ajaxloader('hide');
                    }
                }, 'json');
            }
        });                
    });
</script>