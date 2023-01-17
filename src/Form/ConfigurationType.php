<?php

namespace App\Form;

use App\Entity\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logo', FileType::class, [
                'label' => 'Votre logo (Image file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '5024k',
                        /* 'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document', */
                    ])
                ],
            ])
            ->add('titre')
            ->add('description')
            ->add('service_titre')
            ->add('contact_titre')
            ->add('contact_desc')
            ->add('telephone')
            ->add('adresse')
            ->add('mail')
            ->add('facebook')
            ->add('linkedin')
            ->add('twitter')
            ->add('footer_desc')

            ->add('submit', SubmitType::class, [
                'label'=> "Modifier"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configuration::class,
        ]);
    }
}
