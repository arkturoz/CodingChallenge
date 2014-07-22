<?php

namespace MyDogs\DogBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use MyDogs\DogBundle\Model\Dog;
use MyDogs\DogBundle\Model\DogQuery;


class DogController extends Controller
{
    public function indexAction()
    {
        $dogs = DogQuery::create()->find();
        
        return $this->render('MyDogsDogBundle:Default:index.html.twig', 
                array('dogs' => $dogs));
    }
    
    public function newAction(Request $request)
    {
        $dog = new Dog();

        $breeds = \MyDogs\DogBundle\Model\BreedQuery::create()->find();
        
        foreach ( $breeds as $breed) {
            $choices[$breed->getId()] = $breed->getBreed();
        }
        
        $form = $this->createFormBuilder($dog)
            ->add('name', 'text')
            ->add('sex', 'choice', array('choices' => array('m' => 'Male', 
                    'f' => 'Female')))
            ->add('dob', 'date')
            ->add('breed_id', 'choice', array('choices' => $choices))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $dog->setName($form->get("name")->getData());
            $dog->setSex($form->get("sex")->getData());
            $dog->setDob($form->get("dob")->getData());
            $dog->setBreedId($form->get("breed_id")->getData());
            
            $dog->save();
            
            return $this->redirect($this->generateUrl("my_dogs_dog_homepage"));
        }
        
        return $this->render('MyDogsDogBundle:Dog:new.html.twig', 
                array('form' => $form->createView()));
    }

    public function deleteAction($id)
    {
        $dog = DogQuery::create()->findOneById($id);
        
        $dog->delete();
        
        return $this->redirect($this->generateUrl("my_dogs_dog_homepage"));
    }    
}
