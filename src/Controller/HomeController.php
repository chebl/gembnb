<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller { 
    
    /**
     * @Route("/hello/{prenom}/age/{age}",name="hello")
     * @Route("/hello")
     * @Route("/hello/{prenom}")
     */

    public function hello($prenom='anonyme',$age=0){
        return $this->render(
            'hello.html.twig',
            ['prenom'=>$prenom,
            'age'=>$age,
           
        ]);
    } 
    /**
     * @Route("/",name="homepage")
     */
    public function home(){
        $prenoms=['Lior'=>31,'Joseph'=>12,'Anne'=>55];
        return $this->render(
            'home.html.twig',
            ['title'=>"Aurevoir tout le monde",
            'age'=>13,
            'tableau'=>$prenoms
            ]
        );
    }
}
?>