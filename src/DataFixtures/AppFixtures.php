<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Helper\UploaderHelper;
use App\Helper\VideoLinkFormatter;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Video;
use DateTime;
use Faker;

class AppFixtures extends Fixture
{


    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var VideoLinkFormatter
     */
    private $videoLinkFormatter;


    public function __construct(VideoLinkFormatter $videoLinkFormatter, UploaderHelper $uploaderHelper)
    {
        $this->videoLinkFormatter = $videoLinkFormatter;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Faker\Factory::create();
        $users = [];

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($faker->password());
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setAvatar($faker->imageUrl());
            $manager->persist($user);
            $users[] = $user;
        }
        $categories = [];

        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->text(255));
            $manager->persist($category);
            $categories[] = $category;
        }


        $videos = [
            'http://www.youtube.com/watch?v=AzJPhQdTRQQ&t=1s',
            'https://www.youtube.com/watch?v=axNnKy-jfWw',
            'www.youtube.com/watch?v=axNnKy-jfWw',
            'https://youtu.be/R2Cp1RumorU',
            'https://youtu.be/UGdif-dwu-8',
            'https://www.youtube.com/embed/M_BOfGX0aGs',
            'https://www.dailymotion.com/video/x4b4ga',
            'wwww.dailymotion.com/video/x2j4bgs',
            'dailymotion.com/video/xwpx9p',
            'https://dai.ly/xog7m7',
            'dai.ly/x72qhs9',
            'https://www.dailymotion.com/embed/video/x7vau0d',
            'dailymotion.com/embed/video/x6xb7gd',
            'https://vimeo.com/56415173',
            'vimeo.com/151351853',
            'https://vimeo.com/56688915',
            'vimeo.com/159485768',
            'http://vimeo.com/6097400',
            'https://player.vimeo.com/video/17859252',
        ];

        for ($i = 0; $i < 30; ++$i) {
            $trick = new Trick();
            $trick->setName($this->faker->word)
                ->setDescription($this->faker->text(mt_rand(200, 3000)))
                ->setAuthor($this->getReference('user' . mt_rand(0, 29)))
                ->setCreatedat($this->faker->dateTimeBetween('-30 days', '-15 days', null))
                ->setUpdatedat($this->faker->dateTimeBetween('-15 days', 'now', null))
                ->setMainImage($this->fakeUploadImage($i));
        }
        for ($k = 0; $k < mt_rand(0, 4); ++$k) {
            $image = new Image();
            $image->setName($this->fakeUploadImage($i));
            $trick->addImage($image);
        }
        for ($k = 0; $k < mt_rand(1, 4); ++$k) {
            $video = new Video();
            $formattedName = $this->videoLinkFormatter->format($videos[mt_rand(0, \count($videos) - 1)]);
            $video->setName($formattedName);
            $trick->addVideo($video);
        }
        $manager->persist($trick);

        $comments = [];

        for ($i = 0; $i < 10; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->text(6000));
            $comment->setCreatedAt(new DateTime());
            $comment->setAuthor($users[$faker->numberBetween(1, 9)]);
            $comment->setTrick($trick[$faker->numberBetween(1, 9)]);
            $manager->persist($comment);
            $comments[] = $comment;
        }

        $manager->flush();
    }

    private  function fakeUploadImage($trickId): string
    {
        $trickImages = ['home.jpeg', 'im2.jpeg', 'im3.jpg', 'ima4.jpg', 'img5.jpg', 'img6.jpg', 'image7.jpg', 'img8.jpg', 'img9.jpg'];
        $randomImage = $this->faker->randomElement($trickImages);
        $fileSystem = new Filesystem();
        $targetPath = sys_get_temp_dir() . '/' . $randomImage;
        $fileSystem->copy(__DIR__ . '/images/trick/' . $randomImage, $targetPath, true);

        return $this->uploaderHelper
            ->uploadFile(new File($targetPath), 'tricks', 'trick_' . ($trickId + 1) . '/');
    }
}
