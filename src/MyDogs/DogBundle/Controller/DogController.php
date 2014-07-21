<?php

namespace MyDogs\DogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DogController extends Controller
{
    public function indexAction()
    {
        return $this->render('MyDogsDogBundle:Default:index.html.twig', array('name' => "Arturo"));
    }
}
