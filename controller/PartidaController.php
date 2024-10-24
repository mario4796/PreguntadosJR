<?php
class PartidaController
{

    private $model;
    private $presenter;

    public function __construct($model, $presenter)
    {
        $this->model = $model;
        $this->presenter = $presenter;
    }

    public function nueva()
    {
        $data['respuestaDada'] = false;
        $data['pregunta'] = $this->model->getPregunta();
        $_SESSION['pregunta'] = $data['pregunta'][0]['id'];;
        $_SESSION['prueba'] =$data['pregunta'];
        $this->presenter->show('partidaNueva', $data);
    }

    public function validarRespuesta()
    {

        $repuestaSeleccionada = $_POST['opcion'];
        $pregunta = $_SESSION['pregunta'];
        $data['correcta'] = $this->model->verificarPregunta($pregunta, $repuestaSeleccionada);
        $data['respuestaDada'] = true;
        $data['pregunta'] =$_SESSION['prueba'];
        unset($_SESSION['prueba']);
        unset($_SESSION['pregunta']);
        $this->presenter->show('partidaNueva', $data);

    }
}