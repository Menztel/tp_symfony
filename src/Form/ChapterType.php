<?php

namespace App\Form;

use App\Entity\Chapter;
use App\Entity\Subject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class ChapterType extends AbstractType
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
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ]);

        // Si l'utilisateur est un admin, il peut choisir n'importe quelle matière
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'label' => 'Matière',
                'attr' => ['class' => 'form-control']
            ]);
        } else {
            // Sinon, le professeur ne peut choisir que parmi ses matières
            $builder->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'label' => 'Matière',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.teacher = :teacher')
                        ->setParameter('teacher', $this->security->getUser());
                }
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapter::class,
        ]);
    }
}
