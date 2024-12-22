<?php

namespace App\Form;

use App\Entity\Subject;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('teacher', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'label' => 'Professeur',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function ($er) {
                    $conn = $er->getEntityManager()->getConnection();
                    $sql = 'SELECT u.id FROM "user" u WHERE u.roles::jsonb @> $1';
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(1, '["ROLE_TEACHER"]');
                    $result = $stmt->executeQuery();
                    $ids = array_column($result->fetchAllAssociative(), 'id');
                    
                    if (empty($ids)) {
                        $ids = [-1];
                    }
                    
                    return $er->createQueryBuilder('u')
                        ->where('u.id IN (:ids)')
                        ->setParameter('ids', $ids)
                        ->orderBy('u.lastName', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subject::class,
        ]);
    }
}
