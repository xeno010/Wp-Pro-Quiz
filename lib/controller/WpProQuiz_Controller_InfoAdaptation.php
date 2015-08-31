<?php

class WpProQuiz_Controller_InfoAdaptation extends WpProQuiz_Controller_Controller
{

    public function route()
    {
        $this->showAction();
    }

    private function showAction()
    {
        $view = new WpProQuiz_View_InfoAdaptation();

        $view->show();
    }
}