<?php

class Router
{
    private AuthController $ac;
    private ForumController $fc;

    public function __construct()
    {
        $this->ac = new AuthController();
        $this->fc = new ForumController();
    }
    public function handleRequest(array $get) : void
    {
        if(!isset($get["route"]))
        {
            $this->fc->home();
        }
        else if(isset($get["route"]) && $get["route"] === "register")
        {
            $this->ac->register();
        }
        else if(isset($get["route"]) && $get["route"] === "check-register")
        {
            $this->ac->checkRegister();
        }
        else if(isset($get["route"]) && $get["route"] === "login")
        {
            $this->ac->login();
        }
        else if(isset($get["route"]) && $get["route"] === "check-login")
        {
            $this->ac->checkLogin();
        }
        else if(isset($get["route"]) && $get["route"] === "logout")
        {
            $this->ac->logout();
        }
        else if(isset($get["route"]) && $get["route"] === "category")
        {
            if(isset($get["category_id"]))
            {
                $this->fc->category($get["category_id"]);
            }
            else
            {
                $this->fc->home();
            }
        }
        else if(isset($get["route"]) && $get["route"] === "post")
        {
            if(isset($get["post_id"]))
            {
                $this->fc->post($get["post_id"]);
            }
            else
            {
                $this->fc->home();
            }
        }
        else if(isset($get["route"]) && $get["route"] === "check-comment")
        {
            $this->fc->checkComment();
        }
        else
        {
            $this->fc->home();
        }
    }
}