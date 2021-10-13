<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\helper\FileUploader;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Video;
use Cocur\Slugify\Slugify;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use DateTime;
use Faker;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var UploaderHelper
     */
    private $faker;



    public function __construct(UserPasswordEncoderInterface $encoder, FileUploader $FileUploader)
    {

        $this->encoder = $encoder;
        $this->uploaderHelper = $FileUploader;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $roles = [['ROLE_ADMIN'], ['ROLE_USER'], ['ROLE_MODERATOR']];

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, $this->faker->word);
            $user->setUsername($this->faker->word)
                ->setEmail($this->faker->email)
                ->setPassword($password)
                ->setFirstName($this->faker->firstName(null))
                ->setLastName($this->faker->lastName)
                ->setAvatar("image1.jpg")
                ->setRoles($roles[mt_rand(0, 2)]);
            $manager->persist($user);

            $this->addReference('user' . $i, $user);
        }

        $fakeUsers = [
            'User' => [
                'User1*',
                ['ROLE_USER'],
            ],
            'Moderator' => [
                'Moderator1*',
                ['ROLE_MODERATOR'],
            ],
            'Admin' => [
                'Admin1*',
                ['ROLE_ADMIN'],
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
        $mute->setMainImage("image1.jpg");
        $mute->setCreatedat(new \DateTime('now'));

        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $mute->addImage($image);
        $mute->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $sade = new Trick();
        $sade->setAuthor($user);
        $sade->setName("Sade");
        $sade->setDescription("Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.");
        $sade->setMainImage("image1.jpg");
        $sade->setCreatedat(new \DateTime('now'));

        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $sade->addImage($image);
        $sade->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $indy = new Trick();
        $indy->setAuthor($user);
        $indy->setName("Indy");
        $indy->setDescription("Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.");
        $indy->setMainImage("image1.jpg");
        $indy->setCreatedat(new \DateTime('now'));

        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $indy->addImage($image);
        $indy->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $stalefish = new Trick();
        $stalefish->setAuthor($user);
        $stalefish->setName("Stalefish");
        $stalefish->setDescription("Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.");
        $stalefish->setMainImage("image1.jpg");
        $stalefish->setCreatedAt(new \DateTime('now'));

        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $stalefish->addImage($image);
        $stalefish->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $tailGrab = new Trick();
        $tailGrab->setAuthor($user);
        $tailGrab->setName("Tail Grab");
        $tailGrab->setDescription("Saisie de la partie arrière de la planche, avec la main arrière.");
        $tailGrab->setMainImage("image1.jpg");
        $tailGrab->setCreatedat(new \DateTime('now'));


        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $tailGrab->addImage($image);
        $tailGrab->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $noseGrab = new Trick();
        $noseGrab->setAuthor($user);
        $noseGrab->setName("Nose Grab");
        $noseGrab->setDescription("Saisie de la partie avant de la planche, avec la main avant.");
        $noseGrab->setMainImage("image1.jpg");
        $noseGrab->setCreatedat(new \DateTime('now'));


        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $noseGrab->addImage($image);
        $noseGrab->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $r180 = new Trick();
        $r180->setAuthor($user);
        $r180->setName("180°");
        $r180->setDescription("Rotation de la planche à 180°.");
        $r180->setMainImage("image1.jpg");
        $r180->setCreatedAt(new \DateTime('now'));


        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $r180->addImage($image);
        $r180->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $r360 = new Trick();
        $r360->setAuthor($user);
        $r360->setName("360°");
        $r360->setDescription("Rotation de la planche à 360°.");
        $r360->setMainImage("image1.jpg");
        $r360->setCreatedAt(new \DateTime('now'));


        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $r180->addImage($image);
        $r180->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $r540 = new Trick();
        $r540->setAuthor($user);
        $r540->setName("540°");
        $r540->setDescription("Rotation de la planche à 540°.");
        $r540->setMainImage("image1.jpg");
        $r540->setCreatedAt(new \DateTime('now'));

        $image->setName('image1.jpg');
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $r540->addImage($image);
        $r540->addVideo($video);
        //
        $image = new Image();
        $video = new Video();

        $r720 = new Trick();
        $r720->setAuthor($user);
        $r720->setName("720°");
        $r720->setDescription("Rotation de la planche à 720°.");
        $r720->setMainImage('image1.jpg');
        $r720->setCreatedAt(new \DateTime('now'));

        $image->setName("image1.jpg");
        $video->setUrl("https://www.youtube.com/embed/OparOr70iu0");

        $r720->addVideo($video);
        $r720->addImage($image);

        $manager->persist($mute);
        $manager->persist($sade);
        $manager->persist($indy);
        $manager->persist($stalefish);
        $manager->persist($tailGrab);
        $manager->persist($noseGrab);
        $manager->persist($r180);
        $manager->persist($r360);
        $manager->persist($r540);
        $manager->persist($r720);

        $manager->flush();
    }
}
