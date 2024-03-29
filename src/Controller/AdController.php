<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function create(Request $request){
        $ad=new Ad();
     
        $form=$this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $entityManager->persist($image);
            } 
            $ad->setAuthor($this->getUser());

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
     * Permet de modifier une annonce
     *
     * @Route("/ads/{slug}/edit",name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user===ad.getAuthor()",message="Cette annonce ne vous appartient pas,vous ne pouvez pas la modifier")
     * @return Response
     */
    public function edit(Ad $ad,Request $request){
        
     
        $form=$this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $entityManager->persist($image);
            }
            $entityManager->persist($ad);
            $entityManager->flush();
            $this->addFlash('success',
            "Les modifications de l'annonce <strong>".$ad->getTitle()."</strong> ont bien été enregistrée !"
            ); 
            return $this->redirectToRoute('ads_show',[
                'slug'=>$ad->getSlug()
            ]);
        }  
        return $this->render('ad/edit.html.twig',[
            'form'=>$form->createView(),
            'ad' =>$ad
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
     /**
     * Permet de modifier une annonce
     *
     * @Route("/ads/{slug}/delete",name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user===ad.getAuthor()",message="Cette annonce ne vous appartient pas,vous ne pouvez pas la supprimer")
     * @return Response
     */
    public function delete(Ad $ad){
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ad);
        $entityManager->flush();
        $this->addFlash('success',
        "L'annonce <strong>".$ad->getTitle()."</strong> a bien été supprimé  !"
        ); 
       
        return $this->redirectToRoute('ads_index');
    }
}