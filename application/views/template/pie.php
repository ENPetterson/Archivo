<script>
    $(function(){
        var metodo = window.location.pathname.slice(1);
        $.post('/permiso/getPermisosVista', {vista: metodo}, function(datos){
            $.each(datos, function(index, elemento){
               if (elemento.tipo == 'H'){
                   $(elemento.elemento).hide();
               } 
               if (elemento.tipo == 'D'){
                   if ($(elemento.elemento).is("div")){
                       disableDiv(elemento.elemento);
                   } else {
                       $(elemento.elemento).prop('disabled', true);
                   }
                   
               }
            });
        }, 'json');
    });
</script>
</body>
