<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Student;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new Admin();
        $user->setFirstName("Ali");
        $user->setLastName("Lahbib");
        $user->setPhone("0123456789");
        $user->setDepartment("info");

        $user->setRoles(['ROLE_ADMIN']);
        $user->setEmail('admin@admin.com');
        $user->setPassword('$2y$13$2pWJq7i3GvuvWjjdxOAxBerYo7aNe4YHcp7MGKADnumW1XjSxnQk.'); // 0000
        $manager->persist($user);
        $manager->flush();
        $manager->flush();
    }
}
