<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 12; $i++) {

            $product = new Product();
            $product->setName($faker->name);
            $product->setPrice($faker->randomFloat(2, 1, 100));
            $product->setShortDescription($faker->text( 20));
            $product->setFullDescription($faker->text());
            $product->setPicture('ananas.jpg');
            $manager->persist($product);
        }

        $manager->flush();
    }
}
