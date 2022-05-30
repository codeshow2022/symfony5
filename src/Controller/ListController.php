<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ListController
 */
class ListController
{

    /**
     * @Route(path="/test", name="test_route")
     */
    public function list(){
        return new Response('test response');
    }

}
