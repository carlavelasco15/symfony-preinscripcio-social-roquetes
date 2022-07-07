<?php

namespace App\Form;

use App\Entity\Search;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('field', ChoiceType::class, [
                'choices' => $options['field_choices']
            ])
            ->add('value', TextType::class, [
                'required' => false,
                'empty_data' => ''
            ])
            /* ->add('orderField', ChoiceType::class, [
                'choices' => $options['order_choices'],
            ])
            ->add('order', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Asc' => 'ASC',
                    'Desc' => 'DESC'
                ],
                'choice_attr' => [
                    'Asc' => ['checked' => 'checked'],
                ],
                'label_attr' => [
                    'class' => 'radio-inline'
                ] 
            ])
            ->add('limit', NumberType::class, [
                'required' => true,
                'html5' => true,
                'attr' => ['min' => 1],
            ]) */
            ->add('Buscar', SubmitType::class)
            ;
        }
                
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,

            'field_choices' => ['ID' => 'id'],
            'order_choices' => ['ID' => 'id'],
        ]);
    }
}
