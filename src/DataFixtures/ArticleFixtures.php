<?php

namespace App\DataFixtures;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        //créer 3 categories fakées
        for($j =1 ; $j<=3 ;$j++){
            $category= new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());
            $manager->persist($category);
            //créer entre 4 et 6 article
            for($i =1;$i <=mt_rand(4,6); $i++){
                $article = new Article();
                $content= '<p>' .join($faker->paragraphs(5),'</p><p>') . '</p>';
                $article-> setTitle($faker->sentence())
                    -> setContent($content)
                    ->setImage($faker->image())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'));

                $manager->persist($article);


                // créer des commentaires a l'article(entre 4 et 10)
                for($k =1 ; $k<=3 ;$k++){
                    $comment = new Comment();
                    $content= '<p>' .join($faker->paragraphs(2),'</p><p>') . '</p>';
                    $days= (new \DateTime())->diff($article->getCreatedAt())->days;
                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setArticle($article)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'));
                    $manager->persist($comment);

                }
            }
        }

        $manager->flush();
    }
}
