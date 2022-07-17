<?php
class Util extends MY_AuthController{
    public function __construct() {
        parent::__construct();
    }
    
    public function buscarDuplicado(){
        $tabla = $this->input->post('tabla');
        $campo = $this->input->post('campo');
        $valor = $this->input->post('valor');
        $id = $this->input->post('id');
        $this->load->model('Util_model');
        $resultado = $this->Util_model->buscarDuplicado($tabla, $campo, $valor, $id);
        echo json_encode(array('resultado'=>$resultado));
    }
    
    public function grid2Excel(){
        $titulo = $this->input->post('title');
        $datos = array();
        $tituloColumna = (array) json_decode($this->input->post('columnTitle'));
        $arreglo = json_decode($this->input->post('data'));
        foreach ($arreglo as $item) {
            $datos[] = (array) $item;
        }
        $this->load->library('excel');
        $sheet = new PHPExcel();
        $sheet->getProperties()->setTitle($titulo)->setDescription($titulo);
        $sheet->setActiveSheetIndex(0);
        $sheet->getActiveSheet()->fromArray($tituloColumna, NULL, 'A1');
        $sheet->getActiveSheet()->fromArray($datos, NULL, 'A2');
        $sheet_writer = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $titulo .date('dMy').'.xls"');
        header('Cache-Control: max-age=0');
        $sheet_writer->save('php://output');
    }
}