<div id="pieRegistro">
    <table>
        <tr id="textoPie">
            <td style="padding-top:10px; padding-left:10px">
                El cierre ocurrira dentro de:  
            </td>
            <td>
                <div id="cuentaRegresiva"></div>
            </td>
        </tr>
    </table>
</div>
<script>
    $(window).bind("load", function() { 

    });    
    $(function(){

        var footerHeight = 0,
            footerTop = 0,
            $footer = $("#pieRegistro");

        positionFooter();

        function positionFooter() {

                 footerHeight = $footer.height();
                 footerTop = ($(window).scrollTop()+$(window).height()-footerHeight)+"px";

                if ( ($(document.body).height()+footerHeight) < $(window).height()) {
                    $footer.css({
                         position: "absolute"
                    }).animate({
                         top: footerTop
                    })
                } else {
                    $footer.css({
                         position: "static"
                    })
                }

        }
        $(window)
                .scroll(positionFooter)
                .resize(positionFooter)

        $.post('registro/getCierreActual', function(cierre){
             if (cierre.cerrado){
                 periodoCerrado();
             } else {
                 fechaCierre = moment(cierre.fechahora).format('YYYY/MM/DD HH:mm:ss');
                 $('#cuentaRegresiva').countdown(fechaCierre, function(event) {
                 var $this = $(this).html(event.strftime(''
                    + ' <span>%d</span> días '
                    + '<span>%H</span> horas '
                    + '<span>%M</span> minutos '
                    + '<span>%S</span> segundos'));
                  });
                  $('#cuentaRegresiva').on('finish.countdown', function(){
                      periodoCerrado();
                  });
             }
        }, 'json');

        function periodoCerrado(){
            $("#botonera").hide();
            $('#textoPie').html('<td style="padding-top:10px; padding-left:10px">No hay licitaciones abiertas</td>');
        }

    });
</script>
