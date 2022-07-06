<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\AgeRange;
use App\Entity\Category;
use App\Repository\AgeRangeRepository;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('is_free', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Gratuït' => 1,
                    'De pagament' => 0,
                ],
                'choice_attr' => [
                    'Gratuït' => ['checked' => 'checked']
                ],
                'label_attr' => [
                    'class' => 'radio-inline'
                    ]
                ])
            ->add('worker', TextType::class, [
                'required' => false
                    ])
            ->add('places_total', NumberType::class, [
                'empty_data' => 0,
                'html5' => true
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
            ->add('start_date', DateType::class, [
                'widget' => 'single_text',
                ])
            ->add('end_date', DateType::class, [
                'widget' => 'single_text',
                ])
            ->add('ageRange', EntityType::class, [
                'class' => AgeRange::class,
                'choice_label' => 'name',
                'label' => 'Edat',
                'query_builder' => function (AgeRangeRepository $er) {
                    return $er->createQueryBuilder('a')
                    ->orderBy('a.id', 'ASC');
                }
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Edat',
                'query_builder' => function (CategoryRepository $er) {
                    return $er->createQueryBuilder('a')
                    ->orderBy('a.id', 'ASC');
                }
            ])
            /* ->add('category', ChoiceType::class) */
            ->add('weekday', ChoiceType::class, [
                'mapped' => false,
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'Dilluns' => 'dilluns',
                    'Dimarts' => 'dimarts',
                    'Dimecres' => 'dimecres',
                    'Dijous' => 'dijous',
                    'Divendres' => 'divendres',
                    'Dissabte' => 'dissabte',
                    'Diumenge' => 'diumenge',
                ],
                'label_attr' => [
                    'class' => 'checkbox-inline'
                ]
            ])
            ->add('start_hour', TimeType::class, [ 
                'mapped' => false,
                'input'  => 'datetime',
                'widget'=> 'choice',
            ])
            ->add('end_hour', TimeType::class, [
                'mapped' => false,
                'input'  => 'datetime',
                'widget' => 'choice',
            ])
            ->add('Crear', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary my-3']
            ])
                    
            ;
        }
                
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
