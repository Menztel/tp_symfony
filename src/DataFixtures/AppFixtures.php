<?php

namespace App\DataFixtures;

use Nelmio\Alice\Loader\NativeLoader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $loader = new NativeLoader();
        
        // Définir la fonction de hachage de mot de passe
        $loader->getFakerGenerator()->addProvider($this);
        
        // Chargement des fichiers de fixtures
        $objectSet = $loader->loadFiles([
            __DIR__ . '/../../fixtures/users.yaml',
            __DIR__ . '/../../fixtures/subjects.yaml',
            __DIR__ . '/../../fixtures/chapters.yaml',
            __DIR__ . '/../../fixtures/exercises.yaml',
            __DIR__ . '/../../fixtures/comments.yaml',
        ]);

        // Persistance des objets
        foreach ($objectSet->getObjects() as $object) {
            if ($object instanceof User) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $object,
                    $object->getPassword()
                );
                $object->setPassword($hashedPassword);
            }
            $manager->persist($object);
        }

        $manager->flush();
    }

    public function hashPassword(string $plainPassword): string
    {
        $user = new User();
        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }
}
