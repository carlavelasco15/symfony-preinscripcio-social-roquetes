<?php

namespace App\Form;

use App\Entity\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class EntityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, )
            ->add('location', TextType::class, [
                'attr' => ['placeholder' => 'ex. C/ Aiguablava, 121. 08033 Barcelona'],
                'required' => false
            ])
            ->add('phone', TextType::class, [
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'required' => false
            ])
            ->add('web', TextType::class, [
                'required' => false
            ])
            ->add('picture', FileType::class, [
                'required' => false,
                'data_class' => NULL,
                'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Has de pujar una imatge png, jpg o gif'
                    ])
                ]
            ])
            ->add('schedule', TextareaType::class, [
                'required' => false
            ])
            ->add('Guardar', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary my-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entity::class,
        ]);
    }
}
