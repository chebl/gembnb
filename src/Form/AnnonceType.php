<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnonceType extends AbstractType
{
    
    /**
     * Permet d'avoir la configuration de base d'un champ !
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getConfiguration($label,$placeholder){
        return [
            'label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder
            ]
        ];
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,$this->getConfiguration("Titre","Tapez un super titre pour votre annonce"))
            ->add('slug',TextType::class,$this->getConfiguration("Adresse web","Tapez l'adresse Web(Automatique)"))
            ->add('coverImage',UrlType::class,$this->getConfiguration("Url de l'image principale","Donner l'adresse d'une image qui donne vraiment envie"))
            ->add('introduction',TextType::class,$this->getConfiguration("Introduction","Donner une description globale de l'annonce"))
            ->add('content',TextareaType::class,$this->getConfiguration("Description detaillée","Tapez une description que donne vraiment envie de venir chez vous"))
            ->add('rooms',IntegerType::class,$this->getConfiguration("Nombre de chambres","Le nombre de chambres"))
            ->add('price',MoneyType::class,$this->getConfiguration("Prix par nuit","Indiquer le prix que vous voulez pour une nuit ")); 
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
