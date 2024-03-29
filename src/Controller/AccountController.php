<?php 

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends Controller { 
   
    /**
     * permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     */ 
    public function login(AuthenticationUtils $utils)
    {
        $error=$utils->getLastAuthenticationError();
        $username=$utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError' => $error!==null,
            'username' =>$username
        ]);
    }
    /**
     * permet de se déconnecter 
     * 
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
        //rien ....
    }
     /**
     * permet d'afficher le formulaire d'inscription 
     * 
     * @Route("/register", name="account_register")
     */
    public function register(Request $request,UserPasswordEncoderInterface $encoder )
    {
        $user=new User();
     
        $form=$this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
        $hash=$encoder->encodePassword($user,$user->getHash());
        $user->setHash($hash);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();
        $this->addFlash('success',
            "Votre compte a bien été créé ,  vous pouvez maintenant vous connecter !"
            ); 
            return $this->redirectToRoute('account_login');
    }
    return $this->render('account/registration.html.twig',[
        'form'=>$form->createView()
        
    ]);
   
  }
    /**
     * permet d'afficher et de traiter le formulaire de modification de profil 
     * 
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profile(Request $request){

        $user=$this->getUser();
     
        $form=$this->createForm(AccountType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success',
            "Les données du profil ont été enregistrée avec succés !"
            ); 
           
        }     
        return $this->render('account/profile.html.twig',[
            'form' =>$form->createView()
        ]);
    }
     /**
     * permet de modifier le mot de passe 
     * 
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder){
        $passwordUpdate=new PasswordUpdate();
        $user=$this->getUser();

        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
         if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())){
          $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
         }
         else{
             $newPassword=$passwordUpdate->getNewPassword();
             $hash= $encoder->encodePassword($user,$newPassword);
             
             $user->setHash($hash);
             
             $manager = $this->getDoctrine()->getManager();
             $manager->persist($user);
             $manager->flush();
             
             $this->addFlash('success',
             "Votre mot de passe a bien été modifié !"
            );

            return $this->redirectToRoute('homepage');
         }
    }
        return $this->render('account/password.html.twig',[
            'form' =>$form->createView()
        ]);
    }
    /**
     * permet d'afficher le profil de l'utilisateur connecté  
     * 
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     */
    public function myAccount(){

        return $this->render('user/index.html.twig',[
            'user' =>$this->getUser()
        ]);

    }
      /**
     * permet d'afficher la liste des réservations de l'utilisateur connecté  
     * 
     * @Route("/account/bookings", name="account_bookings")
     *  
     */
    public function bookings(){

        return $this->render('account/bookings.html.twig',[
             
        ]);

    }
    
    
}
?>