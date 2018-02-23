<?php

require_once 'lib/Controller.php';

class CancionController extends Controller
{
    public function __construct()
    {
        $this->setNumImages(0);
        $this->setNumFiles(0);
    }
    public function grid()
    {
        /*$objCancion = new CancionModel();
        $request['columnas'] = '*';
        $data = $objCancion->getData($request);*/
        $this->setView('views/cancionList.php');
        $this->setJs('js/cancionList.js');
        $param = [
            'api_key' => 'd7fe1c7b62c71c3867fe72c8c9862c0a',
            'method' => 'chart.gettoptracks',
            'format' => 'json',
            'limit' => 900

        ];
        return $this->callWebService($param);
    }

    public function filter()
    {

        if ($_GET['cancion'] != '') {
            $param = [
                'api_key' => 'd7fe1c7b62c71c3867fe72c8c9862c0a',
                'method' => 'track.search',
                'track' => $_GET['cancion']
            ];
        }

      return $this->callWebService($param);
    }

    public function getData()
    {
        $objCancion = new CancionModel();
        $data = $objCancion->getData($_REQUEST);
        $this->setJs('cancion.js');
        echo $data;
    }


    public function addToMyLibrary()
    {
      $objMiCancion = new MiCancionModel();
      $objMiCancion->update($_GET);
      $this->setView('cancionView.tpl.php');
      $this->setJs('cancionView.js');

    }
}
