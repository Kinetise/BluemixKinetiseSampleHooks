<?php

namespace KinetiseSkeleton\Controller\Welcome;

use KinetiseSkeleton\Controller\AbstractController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function indexAction($section = 'index')
    {
        try {
            return $this->getTwig()->render(
                sprintf('welcome/index/%s.html.twig', $section)
            );
        } catch (\Exception $e) {
            return new Response(
                $this->getTwig()->render('error/notFound.html.twig'),
                Response::HTTP_NOT_FOUND
            );
        }
    }
}