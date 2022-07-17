var setDropDown;

function setDropDown(widget, value){
    var items = $(widget).jqxDropDownList('getItems');
    // find the index by searching for an item with specific value.
    var indexToSelect = -1;
    $.each(items, function (index) {
        if (this.value == value) {
            indexToSelect = index;
            return false;
        }
    });
    $(widget).jqxDropDownList({selectedIndex: indexToSelect }); 
}

function getDropDown(widget){
    var valor;
    var index = $(widget).jqxDropDownList('getSelectedIndex'); 
    if (index >= 0){
        var item = $(widget).jqxDropDownList('getSelectedItem');
        valor = item.value;
    } else {
        valor = null;
    }
    return valor;
}

function fechaWidget(fecha){
    var resultado = fecha.split('-');
    var pFecha = resultado[2] + '/' + resultado[1] + '/' + resultado[0];
    return pFecha;
}

function fechaDB(widget){
    fecha = $(widget).jqxDateTimeInput('val', 'date');
    resultado = fecha.toISOString().slice(0, 10).replace(/-/g, '');
    return resultado;
}

function fechaHoraDB(widget){
    fecha = $(widget).jqxDateTimeInput('val', 'date');
    return fecha.toISOString().slice(0, 19).replace('T', ' ');
}

var validateEmail;
function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if( !emailReg.test( $email ) ) {
          return false;
    } else {
          return true;
    }
}

var getLocalization = function () {
    var localizationobj = {};
    localizationobj.pagergotopagestring = "Ir a:";
    localizationobj.pagershowrowsstring = "Mostrar filas:";
    localizationobj.pagerrangestring = " de ";
    localizationobj.pagernextbuttonstring = "siguiente";
    localizationobj.pagerpreviousbuttonstring = "anterior";
    localizationobj.sortascendingstring = "Ordenar Ascendente";
    localizationobj.sortdescendingstring = "Ordenar Descendente";
    localizationobj.sortremovestring = "Quitar Orden";
    localizationobj.firstDay = 1;
    localizationobj.percentsymbol = "%";
    localizationobj.currencysymbol = "$";
    localizationobj.currencysymbolposition = "before";
    localizationobj.decimalseparator = ".";
    localizationobj.thousandsseparator = ",";
    localizationobj.groupsheaderstring = "Arrastre una columna y sueltela aqui para agrupar por esa columna";
    localizationobj.groupbystring = "Agrupar por esta columna";
    localizationobj.groupremovestring = "Quitar de agrupacion";
    localizationobj.filterclearstring = "Limpiar";
    localizationobj.filterstring = "Filtrar";
    localizationobj.filtershowrowstring = "Mostrar filas que:";
    localizationobj.filterorconditionstring = "O";
    localizationobj.filterandconditionstring = "Y";
    localizationobj.filterselectallstring = "(Seleccionar Todo)";
    localizationobj.filterchoosestring =  "Por favor elija:";
    localizationobj.filterstringcomparisonoperators = ['vacias', 'no vacias', 'contienen', 'contienen(dist mayus)',
        'no contiene', 'no contiene(dist mayus)', 'empieza con', 'empieza con(dist mayus)',
        'finaliza con', 'finaliza con(dist mayus)', 'igual', 'igual(dist mayus)', 'nulos', 'no nulos'],
    localizationobj.filternumericcomparisonoperators = ['igual', 'distinto', 'menor que', 'menor o igual', 'mayor que', 
        'mayor o igual', 'nulo', 'no nulo'];
    localizationobj.filterdatecomparisonoperators = ['igual', 'distinto', 'menor que', 'menor o igual', 'mayor que', 
        'mayor o igual', 'nulo', 'no nulo'];
    localizationobj.filterbooleancomparisonoperators = ['igual', 'no igual'];
    localizationobj.validationstring = "El valor ingresado no es valido";
    localizationobj.emptydatastring = "No hay datos para mostrar";    
    var days = {
        // full day names
        names: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
        // abbreviated day names
        namesAbbr: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        // shortest day names
        namesShort: ["D", "L", "Ma", "Mi", "J", "V", "S"]
    };
    localizationobj.days = days;
    var months = {
        // full month names (13 months for lunar calendards -- 13th month should be "" if not lunar)
        names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre",
            "Diciembre", ""],
        // abbreviated month names
        namesAbbr: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic", ""]
    };
    var patterns = {
        d: "dd.MM.yyyy",
        D: "dddd, d. MMMM yyyy",
        t: "HH:mm",
        T: "HH:mm:ss",
        f: "dddd, d. MMMM yyyy HH:mm",
        F: "dddd, d. MMMM yyyy HH:mm:ss",
        M: "dd MMMM",
        Y: "MMMM yyyy"
    }
    localizationobj.patterns = patterns;
    localizationobj.months = months;
    localizationobj.todaystring = "Hoy";
    localizationobj.clearstring = "Limpiar";
    return localizationobj;
}

function aFecha(fecha){
    var anio = parseInt($.jqx.dataFormat.formatdate(fecha, 'yyyy'));
    var mes = $.jqx.dataFormat.formatdate(fecha, 'MM');
    mes--;
    var dia = parseInt($.jqx.dataFormat.formatdate(fecha, 'dd'));
    var resultado = new Date();
    resultado.setFullYear(anio, mes, dia);
    return resultado;
}

function disableDiv(element){
    $(element).append('<div style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:2;opacity:0.4;filter: alpha(opacity = 50)"></div>');
}

function grid2excel(grid, title, showHidden){
    var titles = $(grid).jqxGrid('columns').records;
    var titulosArr = Array();
    var data = $(grid).jqxGrid('getboundrows');
    
    $.each(titles, function(index, titulo){
        if (titulo.columntype != 'checkbox' || index > 0){
            titulosArr.push(titulo.text);
            if (!showHidden){
                if (titulo.hidden){
                    titulosArr.pop();
                    data.forEach(function(obj){
                        delete obj[titulo.datafield];
                    });
                }
            }
        }
    });
    data.forEach(function(obj){
        delete obj['uid'];
        for (var propiedad in obj){
            if (obj.hasOwnProperty(propiedad)){
                if (obj[propiedad] instanceof Date){
                    obj[propiedad] = moment(obj[propiedad]).format("DD/MM/YYYY HH:mm");
                }
            }
        };
    });
    var columnTitle = JSON.stringify(titulosArr);
    var datos = JSON.stringify(data);
    
    $.redirect('/util/grid2Excel', {columnTitle: columnTitle ,data: datos, title: title});
    //$.post('/util/grid2Excel', {data: data, title: title});
}

jQuery.fn.removeAttributes = function() {
  return this.each(function() {
    var attributes = $.map(this.attributes, function(item) {
      return item.name;
    });
    var img = $(this);
    $.each(attributes, function(i, item) {
        if (item != "id"){
            img.removeAttr(item);
        }
    });
  });
}

function validaCuit(sCUIT)
{
    var aMult = '5432765432';
    var aMult = aMult.split('');
    
    if (sCUIT && sCUIT.length == 11)
    {
        aCUIT = sCUIT.split('');
        var iResult = 0;
        for (i = 0; i <= 9; i++)
        {
            iResult += aCUIT[i] * aMult[i];
        }
        iResult = (iResult % 11);
        iResult = 11 - iResult;

        if (iResult == 11)
            iResult = 0;
        if (iResult == 10)
            iResult = 9;

        if (iResult == aCUIT[10] || sCUIT == "___________")
        {
            return true;
        }
    }
    return false;

}