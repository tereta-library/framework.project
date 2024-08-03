<?php declare(strict_types=1);

namespace Builder\Panel\Controller;

use Framework\Application\Manager;
use Framework\Http\Interface\Controller;

class View implements Controller
{
    /**
     * @router expression GET /^\/panel\/(.*)$/Usi
     * @param string $identifier
     * @return string
     */
    public function render(string $identifier): string
    {
        $view = Manager::instance()->getView();

        try {
            return (string) $view->render('panel');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}