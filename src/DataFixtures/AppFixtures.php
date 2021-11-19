<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Helper\UploaderHelper;
use App\Helper\VideoLinkFormatter;
use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Video;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    private $faker;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var VideoLinkFormatter
     */
    private $videoLinkFormatter;

    public function __construct(VideoLinkFormatter $videoLinkFormatter, UserPasswordEncoderInterface $encoder, UploaderHelper $uploaderHelper)
    {
        $this->encoder = $encoder;
        $this->faker = \Faker\Factory::create();
        $this->uploaderHelper = $uploaderHelper;
        $this->videoLinkFormatter = $videoLinkFormatter;
    }


    public function load(ObjectManager $manager)
    {
        $roles = [['ROLE_ADMIN']];

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, $this->faker->word);
            $user->setUsername($this->faker->word)
                ->setEmail($this->faker->email)
                ->setPassword($password)
                ->setFirstName($this->faker->firstName(null))
                ->setLastName($this->faker->lastName)
                ->setAvatar("image1.jpg")
                ->setRoles($roles[mt_rand(0, 1)]);
            $manager->persist($user);

            $this->addReference('user' . $i, $user);
        }

        $fakeUsers = [
            'User' => [
                'User1*',
                ['ROLE_USER'],
            ],
        ];

        foreach ($fakeUsers as $fakeUser) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, $fakeUser[0]);
            $user->setUsername(array_search($fakeUser, $fakeUsers, true))
                ->setEmail($this->faker->email)
                ->setPassword($password)
                ->setRoles($fakeUser[1]);
            $manager->persist($user);
        }
        $manager->flush();

        //Category
        $grabs = new Category();
        $rotations = new Category();
        $flips = new Category();

        $grabs->setName('Grabs');
        $rotations->setName('rotations');
        $flips->setName('flips');

        $manager->persist($grabs);
        $manager->persist($rotations);
        $manager->persist($flips);

        $manager->flush();

        //Tricks
        $image = new Image();
        $video = new Video();

        $mute = new Trick();
        $mute->setAuthor($user);
        $mute->setName("Mute");
        $mute->setDescription("Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.");
        $mute->setMainImage("home.jpg");
        $mute->setCreatedAt(new \DateTime('now'));
        $mute->setCategory($rotations);

        $image->setName('home.jpg');
        $video->setName("https://www.youtube.com/embed/OparOr70iu0");

        $mute->addImage($image);
        $mute->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $sade = new Trick();
        $sade->setAuthor($user);
        $sade->setName("Sade");
        $sade->setDescription("Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.");
        $sade->setMainImage("home.jpg");
        $sade->setCreatedAt(new \DateTime('now'));
        $sade->setCategory($rotations);

        $image->setName('stalefish-1.jpg');
        $video->setName("https://www.youtube.com/embed/OparOr70iu0");

        $sade->addImage($image);
        $sade->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $indy = new Trick();
        $indy->setAuthor($user);
        $indy->setName("Indy");
        $indy->setDescription("Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.");
        $indy->setMainImage("home.jpg");
        $indy->setCreatedAt(new \DateTime('now'));
        $indy->setCategory($rotations);

        $image->setName('home.jpg');
        $video->setName("https://www.youtube.com/embed/OparOr70iu0");

        $indy->addImage($image);
        $indy->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $stalefish = new Trick();
        $stalefish->setAuthor($user);
        $stalefish->setName("Stalefish");
        $stalefish->setDescription("Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.");
        $stalefish->setMainImage("home.jpg");
        $stalefish->setCreatedAt(new \DateTime('now'));
        $stalefish->setCategory($grabs);

        $image->setName('home.jpg');
        $video->setName("https://www.youtube.com/embed/OparOr70iu0");

        $stalefish->addImage($image);
        $stalefish->addVideo($video);
        //

        $manager->persist($mute);
        $manager->persist($sade);
        $manager->persist($indy);
        $manager->persist($stalefish);
        $manager->flush();
    }
}
