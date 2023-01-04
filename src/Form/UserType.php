<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('email')
            //->add('roles')
            ->add('old_password', PasswordType::class,[
                'label'=>'Mon mot de passe actuel',
                "mapped"=>false,
                'attr'=>[
                    'placeholder'=>'Veuillez saisir votre mot de passe actuel',
                ]
            ])
            ->add('new_password', RepeatedType::class,[
                "type"=>PasswordType::class,
                "mapped"=> false,
                "invalid_message"=>'Le mot de passe et la confirmation doivent etre identique',
                "label"=>"Mon nouveau mot de passe",
                "required"=>true,
                'first_options'=>['label'=>'Mon nouveau mot de passe', "attr"=>[
                    'placeholder'=>'Merci de saisir votre nouveau mot de passe'
                ]],
                'second_options'=>['label'=>'Confirmer votre nouveau mot de passe', "attr"=>[
                    'placeholder'=>'Merci de confirmer votre nouveau mot de passe'
                ]]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
