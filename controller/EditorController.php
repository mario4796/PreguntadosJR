<?php

class EditorController
{
    private $model;
    private $presenter;

    public function __construct($model, $presenter)
    {
        $this->model = $model;
        $this->presenter = $presenter;
    }

    public function verPreguntas()
    {
            $data['preguntas'] = $this->model->getTodasLasPreguntasConRespuestas();
            $this->presenter->show('editor', $data);
    }

    public function editarPregunta()
    {
            $id = $_POST['id'] ?? null;

            if ($id) {
                $data['pregunta'] = $this->model->getPreguntaById($id);
                for ($i = 1; $i <= 4; $i++) {
                    $key = "opcion_{$i}";
                    $data["opcion_{$i}_seleccionada"] = ($data['pregunta']['opcion_correcta'] == $data['pregunta'][$key]);
                }
                $this->presenter->show('editarPregunta', $data);
            }
    }

    public function actualizarPregunta()
    {
            $id = $_POST['id'] ?? null;

            if ($id) {
                $nuevaPregunta = $_POST['pregunta'];
                $nuevaCategoria = $_POST['categoria'];
                $opcion1 = $_POST['opcion_1'];
                $opcion2 = $_POST['opcion_2'];
                $opcion3 = $_POST['opcion_3'];
                $opcion4 = $_POST['opcion_4'];
                $opcionCorrecta = $_POST['opcion_correcta'];

                $this->model->actualizarPregunta($id, $nuevaPregunta, $nuevaCategoria, $opcion1, $opcion2, $opcion3, $opcion4, $opcionCorrecta);
                $_SESSION['success'] = "Pregunta actualizada correctamente";
                header("Location: ../usuario/home");
            }
    }

    public function eliminarPregunta()
    {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $this->model->eliminarPregunta($id);
                $_SESSION['success'] = "Pregunta eliminada correctamente";
                header("Location: ../usuario/home");
            }
    }

    public function verPreguntasReportadas()
    {
            $data['preguntas'] = $this->model->getPreguntasReportadas();
            $data['reportadas'] = true;
            if(!$data['preguntas']){
                $data['info'] = "No hay preguntas reportadas";
            }
            $this->presenter->show('editor', $data);
    }

    public function verPreguntasSugeridas()
    {
            $data['preguntas'] = $this->model->getPreguntasSugeridas();
            $data['sugeridas'] = true;
            if(!$data['preguntas']){
                $data['info'] = "No hay preguntas sugeridas";
            }
            $this->presenter->show('editor', $data);
    }

    public function darDeAltaPregunta()
    {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $this->model->aprobarPregunta($id);
                $_SESSION['success'] = "Pregunta dada de alta correctamente";
                header("Location: ../usuario/home");
            }
    }

    public function verPreguntasEliminadas()
    {
        $data['preguntas'] = $this->model->getPreguntasEliminadas();
        $data['eliminadas'] = true;
        if(!$data['preguntas']){
            $data['info'] = "No hay preguntas eliminadas";
        }
        $this->presenter->show('editor', $data);
    }

}
