<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<style>
    .btn {
      background: #E77623;
      background-image: -webkit-linear-gradient(top, #E77623, #F69345);
      background-image: -moz-linear-gradient(top, #E77623, #F69345);
      background-image: -ms-linear-gradient(top, #E77623, #F69345);
      background-image: -o-linear-gradient(top, #E77623, #F69345);
      background-image: linear-gradient(to bottom, #E77623, #F69345);
      -webkit-border-radius: 28;
      -moz-border-radius: 28;
      border-radius: 28px;
      font-family: Arial;
      color: #ffffff;
      font-size: 50px;
      padding: 10px 20px 10px 20px;
      text-decoration: none;
      min-width: 400px;
    }

    .btn:hover {
      background: #CA660E;
      background-image: -webkit-linear-gradient(top, #CA660E, #CA660E);
      background-image: -moz-linear-gradient(top, #CA660E, #CA660E);
      background-image: -ms-linear-gradient(top, #CA660E, #CA660E);
      background-image: -o-linear-gradient(top, #CA660E, #CA660E);
      background-image: linear-gradient(to bottom, #CA660E, #CA660E);
      text-decoration: none;
    }    
    
    table#botonera {
        width: 50%;
        margin-left: 5%;
        margin-right: 25%;
        margin-top: 0.5%;
    }
    
    .texto-cierre {
        font-size: 10pt;
        text-align: center;
        padding-top: 10px;
    }
</style>
<table id="botonera">
<!--    <tr>
        <td><button class="btn" id="btnRegistros">Lecer<div id="cierreRegistros" class="texto-cierre"></div></button></td>
        <td><button class="btn" id="btnLetes">Letes<div id="cierreLetes" class="texto-cierre"></div></button></td>
    </tr>
    <tr>
        <td style="padding-top: 5em"><button class="btn" id="btnBono">Letes 140<div id="cierreBono" class="texto-cierre"></div></button></td>
        <td style="padding-top: 5em"><button class="btn" id="btnCupon">INVJ Cupones<div id="cierreCupon" class="texto-cierre"></div></button></td>
    </tr>-->
<!--    <tr>
        <td style="padding-top: 5em"><button class="btn" id="btnLetesPesos">Lecap<div id="cierreLetesPesos" class="texto-cierre"></div></button></td>
        <td style="padding-top: 5em"><button class="btn" id="btnSenebi">Senebi<div id="cierreSenebi" class="texto-cierre"></div></button></td>
    </tr>
    
    <tr>
        <td style="padding-top: 5em"><button class="btn" id="btnFondo">Fondos<div id="cierreFondo" class="texto-cierre"></div></button></td>
        <td style="padding-top: 5em"><button class="btn" id="btnCorreccionBoletos">Corrección Boletos<div id="correccionBoletos" class="texto-cierre"></div></button></td>
    </tr>-->
        
    <tr>
        <td style="padding-top: 5em"><button class="btn" id="btnRegistro">Registro Pesos<div id="registro" class="texto-cierre"></div></button></td>
    </tr>
    
</table>
<script>
    $(function(){

        $.post('registro/getCierreActual', function(cierre){
             if (cierre.cerrado){
                 periodoCerradoRegistro();
             } else {
                 var fechaCierre = moment(cierre.fechahora).format('YYYY/MM/DD HH:mm:ss');
                 $('#cierreRegistro').countdown(fechaCierre, function(event) {
                 var $this = $(this).html(event.strftime(''
                    + 'Cierre en <span>%w</span> semanas <span>%d</span> días '
                    + '<span>%H</span> horas '
                    + '<span>%M</span> minutos '
                    + '<span>%S</span> segundos'));
                  });
                  $('#cierreRegistro').on('finish.countdown', function(){
                      periodoCerradoRegistro();
                  });
             }
        }, 'json');
        
        function periodoCerradoRegistro(){
            $('#cierreRegistro').html('No hay licitaciones abiertas');
        }

        
        
        $.post('letesPesos/getCierreActual', function(cierre){
             if (cierre.cerrado){
                 periodoCerradoLetesPesos();
             } else {
                 var fechaCierre = moment(cierre.fechahora).format('YYYY/MM/DD HH:mm:ss');
                 $('#cierreLetesPesos').countdown(fechaCierre, function(event) {
                 var $this = $(this).html(event.strftime(''
                    + 'Cierre en <span>%w</span> semanas <span>%d</span> días '
                    + '<span>%H</span> horas '
                    + '<span>%M</span> minutos '
                    + '<span>%S</span> segundos'));
                  });
                  $('#cierreLetesPesos').on('finish.countdown', function(){
                      periodoCerradoLetesPesos();
                  });
             }
        }, 'json');

        function periodoCerradoLetesPesos(){
            $('#cierreLetesPesos').html('No hay licitaciones abiertas');
        }

        
        
        
        $("#btnRegistro").click(function(){
            $.redirect('/registro');
        });
        
    });

</script>