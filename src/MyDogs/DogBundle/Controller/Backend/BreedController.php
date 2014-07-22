<?php

namespace MyDogs\DogBundle\Controller\Backend;

//Importing Symfony specific components
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//Importing the Model classes
use MyDogs\DogBundle\Model\Breed;
use MyDogs\DogBundle\Model\BreedQuery;

class BreedController extends Controller
{
    public function indexAction()
    {
        $breeds = BreedQuery::create()->find();
        
        return $this->render('MyDogsDogBundle:Breed:index.html.twig', 
                array('breeds' => $breeds));
    }
    
    public function newAction(Request $request)
    {
        $breed = new Breed();

        $form = $this->createFormBuilder($breed)
            ->add('breed', 'text')
            ->add('description', 'textarea')
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $breed->setBreed($form->get("breed")->getData());
            $breed->setDescription($form->get("description")->getData());
            $breed->save();
            
            return $this->redirect($this->generateUrl("my_dogs_breed_homepage"));
        }
        
        return $this->render('MyDogsDogBundle:Breed:new.html.twig', 
                array('form' => $form->createView()));
    }
    
    public function deleteAction($id)
    {
        $breed = BreedQuery::create()->findOneById($id);
        
        $breed->delete();
        
        return $this->redirect($this->generateUrl("my_dogs_breed_homepage"));
    }
}
