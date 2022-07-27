<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserNotificationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email_ent_new_prescription', CheckboxType::class, [
                'label' => 'Quan hi ha una nova prescripció al meu equipament.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ent_waiting_list', CheckboxType::class, [
                'label' => 'Quan falta  poc per iniciar una activitat i hi ha usuaris que encara no s\'han inscrit.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ent_finished_activity', CheckboxType::class, [
                'label' => 'Quan s\'ha acabat una activitat del meu equipament (recordatori formulari assistència).',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ent_attendance_form', CheckboxType::class, [
                'label' => 'Quan ja han passat 30 dies de la finalització de l\'activitat i encara no s\'ha tancat el formulari d\'assistència.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ent_participant_rating', CheckboxType::class, [
                'label' => 'Quan un usuari ha emplenat el formulari d\'avaluació d\'una activitat del teu equipament.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ser_new_activity', CheckboxType::class, [
                'label' => 'Quan s\'ha creat una nova activitat.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ser_activity_ended', CheckboxType::class, [
                'label' => 'Quan s\'ha acabat una activitat d\'un dels teus participants.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ser_attendance_form', CheckboxType::class, [
                'label' => 'Quan les dades d\'assistència d\'una de les activitats ja està disponible.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ser_deleted_activity', CheckboxType::class, [
                'label' => 'Quan s\'ha eliminat una activitat d\'un dels teus participants.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
            ])
            ->add('email_ser_from_waiting_to_open', CheckboxType::class, [
                'label' => 'Quan un dels participants que vas apuntar a la llista d\'espera ha passat a estar com a preinscrit.',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch ms-2',
                ],
                'attr' => [
                    'class' => 'fs-5'
                ]
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
