<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
     /**
     * permet d'afficher le formulaire d'inscription 
     * 
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
     
    public function book(Ad $ad,Request $request)
    {
        
        $booking=new Booking();
        
        $form=$this->createForm(BookingType::class,$booking);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $booking->setBooker($this->getUser())
                    ->setAd($ad);
            //Si les dates ne sont pa disponibles
            if(!$booking->isBookableDates()){
                $this->addFlash('warning',
                "Les dates que vous avez choisi ne peuvent être réservées : elles sont déjà prises !"
                );    
            }
            else {
              $entityManager->persist($booking);
              $entityManager->flush();
          
            return $this->redirectToRoute('booking_show',[
                'id'=>$booking->getId(),
                'withAlert'=>true
            ]);
            }  
        }     
        return $this->render('booking/book.html.twig', [
            'form' => $form->createView(),
            'ad'   =>$ad
        ]);
    }
        
    /**
     * Permet d'afficher la page de réservation
     *
     * @Route("/booking/{id}", name="booking_show")
     * @param  Booking $booking
     * @return Response
     */
    public function show(Booking $booking){
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }
}
