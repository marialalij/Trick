<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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

        $tricks = [];

        for ($i = 0; $i < 10; $i++) {
            $trick = new Trick();
            $trick->setName($faker->text(255));
            $trick->setDescription($faker->text(6000));
            $trick->setCreatedat(new DateTime());
            $trick->setUpdatedat(new DateTime());
            $trick->setMainImage($faker->imageUrl());
            $trick->setAuthor($users[$faker->numberBetween(1, 9)]);
            $trick->setCategory($categories[$faker->numberBetween(1, 9)]);
            $manager->persist($trick);
            $tricks[] = $trick;
        }
        $comments = [];

        for ($i = 0; $i < 10; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->text(6000));
            $comment->setCreatedAt(new DateTime());
            $comment->setAuthor($users[$faker->numberBetween(1, 9)]);
            $comment->setTrick($tricks[$faker->numberBetween(1, 9)]);
            $manager->persist($comment);
            $comments[] = $comment;
        }

        $images = [];

        for ($i = 0; $i < 10; $i++) {
            $image = new Image();
            $image->setName($faker->imageUrl());
            $image->setTrick($tricks[$faker->numberBetween(1, 9)]);
            $manager->persist($image);
            $images[] = $image;
        }


        $videos = [];

        for ($i = 0; $i < 10; $i++) {
            $video = new Video();
            $video->setName($faker->imageUrl());
            $video->setTrick($tricks[$faker->numberBetween(1, 9)]);
            $manager->persist($video);
            $videos[] = $video;
        }
        $manager->flush();
    }
}
