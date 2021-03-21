<?php

namespace Chiphpmunk\Module\Home;

use Chiphpmunk\Module\Controller;
use Chiphpmunk\Http\Response;

class HomeController extends Controller
{
    /**
     * Home index
     * 
     * @return Response The HTTP response
     */
    public function index() : Response
    {
        $renderer = $this->components->getRenderer();

        $document = $renderer->render('index@home');

        $response = new Response();
        $response->getBody()->write($document);
        return $response;
    }
}
