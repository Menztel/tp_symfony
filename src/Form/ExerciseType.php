<?php

namespace App\Form;

use App\Entity\Exercise;
use App\Entity\Chapter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class ExerciseType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('difficulty', IntegerType::class, [
                'label' => 'Difficulté',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 5
                ]
            ]);

        // Si l'utilisateur est un admin, il peut choisir n'importe quel chapitre
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('chapter', EntityType::class, [
                'class' => Chapter::class,
                'choice_label' => 'title',
                'label' => 'Chapitre',
                'attr' => ['class' => 'form-control']
            ]);
        } else {
            // Sinon, le professeur ne peut choisir que parmi ses chapitres
            $builder->add('chapter', EntityType::class, [
                'class' => Chapter::class,
                'choice_label' => 'title',
                'label' => 'Chapitre',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('c')
                        ->join('c.subject', 's')
                        ->where('s.teacher = :teacher')
                        ->setParameter('teacher', $this->security->getUser());
                }
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercise::class,
        ]);
    }
}
