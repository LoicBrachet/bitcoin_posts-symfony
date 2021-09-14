<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('pseudo')
        ->add('email', EmailType::class)
        ->add('titre', TextType::class)
        ->add('slug', TextType::class, [
            'label'=>'Etiquette'
        ])
        ->add('contenu',TextType::class,[
            'attr' => array('style' => 'height: 400px')
        ])
        ->add('categories', EntityType::class, [
            'class' => Categories::class,
            'multiple' => true,
            'expanded' => true
        ])
        ->add('Publier', SubmitType::class, [
            'label'=>'Valider'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
