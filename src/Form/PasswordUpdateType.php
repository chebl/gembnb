<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
 
class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
        ->add('oldPassword',PasswordType::class,$this->getConfiguration("Ancien Mot de passe",
        "Donner votre mot de passe actuel ..."))
        ->add('newPassword',PasswordType::class,$this->getConfiguration("Nouveau Mot de passe",
        "Tapez votre nouveau mot de passe ..."))
        ->add('confirmPassword',PasswordType::class,$this->getConfiguration("Confirmation de mot de passe",
        "Confirmer votre nouveau mot de passe ..."))
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
