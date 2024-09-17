<?php

class ForumController extends AbstractController
{
    private UserManager $um;

    public function __construct() {
        parent::__construct();
        $this->um = new UserManager();
    }

    //Method displayForum() : Renders the forum landing page.

    public function displayForum() {
        
        $this->render('front/forum/forum.html.twig', []);
        
    }
}