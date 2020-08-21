<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index()
    {
        $repo=$this->getDoctrine()->getRepository(Ad::class);
        $ads=$repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' =>$ads,
        ]);
    }
    /**
     * Permet de créer une annonce
     *
     * @Route("/ads/new",name="ads_create")
     * 
     * @return Response
     */
    public function create(Request $request){
        $ad=new Ad();
        $form=$this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ad);
            $entityManager->flush();
            $this->addFlash('success',
            "L'annonce <strong>".$ad->getTitle()."</strong> a bien été enregistrée !"
            ); 
            return $this->redirectToRoute('ads_show',[
                'slug'=>$ad->getSlug()
            ]);
        } 
        return $this->render('ad/new.html.twig',[
            'form'=>$form->createView()
        ]); 
     } 
    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/ads/{slug}",name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad){
      
       // $ad=$repo->findOneBySlug($slug); //Remplacer par la notion du ParamConverter de Symfony
        
        return $this->render('ad/show.html.twig', [
            'ad' =>$ad,
        ]);
    }
}