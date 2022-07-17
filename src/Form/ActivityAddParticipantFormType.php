<?php

namespace App\Form;

use App\Repository\ParticipantRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Participant;

class ActivityAddParticipantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('participant', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'name',
                'label' => 'Crear tiquet a ',
                'query_builder' => function(ParticipantRepository $er) {
                    return $er->createQueryBuilder('p')
                    ->orderBy('p.name', 'ASC');
                }
            ])
            ->add('Afegir', SubmitType::class, [
                'label' => 'Afegir',
                'attr' => ['class' => 'btn btn-primary my-3']
            ])
            ->setAction($options['action']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
