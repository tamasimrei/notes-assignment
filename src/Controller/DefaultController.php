<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    /**
     * @return Response
     */
    public function getIndexAction(): Response
    {
        return $this->render('index.html.twig');
    }
}
