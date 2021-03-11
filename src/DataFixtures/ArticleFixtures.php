<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        $faker=\Faker\Factory::create('fr_FR');
        for($i=1; $i<10;$i++)
        {
            $category= new Category;
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());
            
        }
    }
}
