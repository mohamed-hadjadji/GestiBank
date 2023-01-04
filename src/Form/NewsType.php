<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title' ,TypeTextType::class,[
                "attr"=>["class"=>"form-control","min-length"=>2,"max-length"=>50,
                "placeholder"=>"Veuillez donner un titre à l'actualité"],
                "label"=>"Titre",
                "label_attr"=>["class"=>"form-label mt-4"],
                
                ])
            ->add('description',TextareaType::class,[
                "attr"=>["class"=>"form-control","placeholder"=>"Veuillez décrire l'actualité"],
                "label"=>"Description",
                "label_attr"=>["class"=>"form-label mt-4"]
            ])
            ->add('image',FileType::class,[
                'data_class' => null,
                "attr"=>["class"=>"form-control"],
                "label"=>"Image",
                "label_attr"=>["class"=>"form-label mt-4"],
                "mapped"=>"false",
                "required"=>"false",
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class,[
                "attr"=>["class"=>"btn btn-primary mt-4"],
                "label"=>"Soumettre"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
