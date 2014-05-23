<?php
namespace Barany\Core;

class View {
    private $layout = 'default';
    private $view = null;

    public function __construct($view = null) {
        $this->view = $view;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function setView($view) {
        $this->view = $view;
    }

    public function render() {
        if ($this->view === null) {
            throw new \RuntimeException('View is null!');
        }

        $layout = file_get_contents(ROOT . '/view/layouts/' . $this->layout . '.html');
        $view = file_get_contents(ROOT . '/view/views/' . $this->view . '.html');

        echo str_replace('{{body}}', $view, $layout);
    }
}