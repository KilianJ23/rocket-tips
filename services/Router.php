<?php

class Router {
    
    public function __construct()
    {
    }

    public function handleRequest(? string $route) : void {
        
        $dc = new DefaultController;
        
        if($route === null)
        {
            $dc->homepage();
        }
        else
        {
            $dc->notFound();
        }
    }
}