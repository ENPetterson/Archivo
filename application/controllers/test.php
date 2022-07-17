<?php 
class Test extends CI_Controller{
    public function index(){
        $orden1 = R::load('lebac',1);
        $orden2 = R::load('lebac',3);
        echo json_encode($orden1->export());
        echo "<br>";
        echo json_encode($orden2->export());
    }
}