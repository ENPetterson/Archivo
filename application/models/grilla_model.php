<?php


class Grilla_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public function datosGrilla($table, $fields, $pagenum, $pagesize, $filterscount, $filtervalue, 
            $filtercondition, $filterdatafield, $filteroperator, $sortdatafield, $sortorder) {
                
        $start = $pagenum * $pagesize;
        $query = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $table;
        $where = ' WHERE 1 = 1 ';
        $order = '';
        $limit = ' LIMIT ' . $start . ', ' . $pagesize;
	// filter data.
        if ($filterscount > 0) {
            $where = " WHERE (";
            $tmpdatafield = "";
            $tmpfilteroperator = "";
            for ($i=0; $i < $filterscount; $i++) {
                // get the filter's value.

                if ($tmpdatafield == "") {
                    $tmpdatafield = $filterdatafield[$i];			
                } else if ($tmpdatafield <> $filterdatafield[$i]) {
                    $where .= ")AND(";
                } else if ($tmpdatafield == $filterdatafield[$i]) {
                    if ($tmpfilteroperator[$i] == 0) {
                        $where .= " AND ";
                    } else {
                        $where .= " OR ";	
                    }
                }

                // build the "WHERE" clause depending on the filter's condition, value and datafield.
                switch($filtercondition[$i]) {
                    case "NOT_EMPTY":
                    case "NOT_NULL":
                        $where .= " " . $filterdatafield[$i] . " NOT LIKE '" . "" ."'";
                        break;
                    case "EMPTY":
                    case "NULL":
                        $where .= " " . $filterdatafield[$i] . " LIKE '" . "" ."'";
                        break;
                    case "CONTAINS_CASE_SENSITIVE":
                        $where .= " BINARY  " . $filterdatafield[$i] . " LIKE '%" . $filtervalue[$i] ."%'";
                        break;
                    case "CONTAINS":
                        $where .= " " . $filterdatafield[$i] . " LIKE '%" . $filtervalue[$i] ."%'";
                        break;
                    case "DOES_NOT_CONTAIN_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield[$i] . " NOT LIKE '%" . $filtervalue[$i] ."%'";
                        break;
                    case "DOES_NOT_CONTAIN":
                        $where .= " " . $filterdatafield[$i] . " NOT LIKE '%" . $filtervalue[$i] ."%'";
                        break;
                    case "EQUAL_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield[$i] . " = '" . $filtervalue[$i] ."'";
                        break;
                    case "EQUAL":
                        $where .= " " . $filterdatafield[$i] . " = '" . $filtervalue[$i] ."'";
                        break;
                    case "NOT_EQUAL_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield[$i] . " <> '" . $filtervalue[$i] ."'";
                        break;
                    case "NOT_EQUAL":
                        $where .= " " . $filterdatafield[$i] . " <> '" . $filtervalue[$i] ."'";
                        break;
                    case "GREATER_THAN":
                        $where .= " " . $filterdatafield[$i] . " > '" . $filtervalue[$i] ."'";
                        break;
                    case "LESS_THAN":
                        $where .= " " . $filterdatafield[$i] . " < '" . $filtervalue[$i] ."'";
                        break;
                    case "GREATER_THAN_OR_EQUAL":
                        $where .= " " . $filterdatafield[$i] . " >= '" . $filtervalue[$i] ."'";
                        break;
                    case "LESS_THAN_OR_EQUAL":
                        $where .= " " . $filterdatafield[$i] . " <= '" . $filtervalue[$i] ."'";
                        break;
                    case "STARTS_WITH_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield[$i] . " LIKE '" . $filtervalue[$i] ."%'";
                        break;
                    case "STARTS_WITH":
                        $where .= " " . $filterdatafield[$i] . " LIKE '" . $filtervalue[$i] ."%'";
                        break;
                    case "ENDS_WITH_CASE_SENSITIVE":
                        $where .= " BINARY " . $filterdatafield[$i] . " LIKE '%" . $filtervalue[$i] ."'";
                        break;
                    case "ENDS_WITH":
                        $where .= " " . $filterdatafield[$i] . " LIKE '%" . $filtervalue[$i] ."'";
                        break;
                }

                if ($i == $filterscount - 1) {
                    $where .= ")";
                }

                $tmpfilteroperator = $filteroperator[$i];
                $tmpdatafield = $filterdatafield[$i];			
            }
        }
	
	if ($sortdatafield != ''){
            if ($sortorder != '') {
                if ($sortorder == "desc") {
                    $order .= " ORDER BY {$sortdatafield} DESC ";
                }
                else if ($sortorder == "asc") {
                    $order = " ORDER BY {$sortdatafield} ASC ";
                }
            }
	}
        
        $total_rows_query = "SELECT COUNT(*) FROM {$table} " . $where;
        
        $total_rows = R::getCell($total_rows_query);
        
        $fullquery = $query . $where . $order . $limit;
        
        log_message('debug', "Query: {$fullquery}");
        
        $datos = R::getAll($fullquery);
        
        $data[] = array(
            'TotalRows' => $total_rows,
            'Rows' => $datos
        );
        return $data;
        
    }
    
    function grillaSinPagina($query){
        $datos = R::getAll($query);
        return $datos;
    }
    
}


