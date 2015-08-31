<?php

class WpProQuiz_Controller_WpqSupport extends WpProQuiz_Controller_Controller
{

    public function route()
    {
        $this->showView();
    }

    private function showView()
    {
        $view = new WpProQuiz_View_WpqSupport();

        $view->show();
    }
}