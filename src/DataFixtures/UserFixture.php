<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UserFixture constructor.
     *
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('root');
        $user->setPassword(
            $this->encoder->encodePassword($user,'secret')
        );
        $user->setEmail('at@dot.me');

        $manager->persist($user);

        $manager->flush();
    }
}
