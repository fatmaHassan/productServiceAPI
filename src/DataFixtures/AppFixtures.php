<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Purchased;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $userFixtureFile = __DIR__ . '/data/users.csv';
    private $productFixtureFile = __DIR__ . '/data/products.csv';
    private $purchasedFixtureFile = __DIR__ . '/data/purchased.csv';
    private UserPasswordEncoderInterface $encoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param string $userFixtureFile
     * @param string $productFixtureFile
     * @param string $purchasedFixtureFile
     */
    public function __construct(UserPasswordEncoderInterface $encoder, string $userFixtureFile = '', string $productFixtureFile = '', string $purchasedFixtureFile = '')
    {
        $userFixtureFile ?? $this->userFixtureFile = $userFixtureFile;
        $productFixtureFile ?? $this->productFixtureFile = $productFixtureFile;
        $purchasedFixtureFile ?? $this->purchasedFixtureFile = $purchasedFixtureFile;
        $this->encoder = $encoder;
    }

    /**
     * Load some data for the user table
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUserData($manager);
        $this->loadProductData($manager);
        $this->loadPurchasedData($manager);
    }

    /**
     * populate User table with given file csv fixture file
     * @param ObjectManager $manager
     */
    public function loadUserData(ObjectManager $manager)
    {
        $fileHandler = fopen($this->userFixtureFile, 'r');
        fgetcsv($fileHandler, 1000, ',');
        while (($line = fgetcsv($fileHandler, 1000, ',')) !== false) {
            $user = new User();
            $user->setId($line[0]);
            $user->setName($line[1]);
            $user->setEmail($line[2]);
            $user->setPassword($this->encoder->encodePassword($user, $line[3]));
            $manager->persist($user);
        }
        $manager->flush();

        fclose($fileHandler);
    }

    /**
     * populate Product table with given file csv fixture file
     * @param ObjectManager $manager
     */
    public function loadProductData(ObjectManager $manager)
    {
        $fileHandler = fopen($this->productFixtureFile, 'r');
        fgetcsv($fileHandler, 1000, ',');
        while (($line = fgetcsv($fileHandler, 1000, ',')) !== false) {
            $product = new Product();
            $product->setSku($line[0]);
            $product->setName($line[1]);
            $manager->persist($product);
        }
        $manager->flush();

        fclose($fileHandler);
    }

    /**
     * populate Purchased table with given file csv fixture file
     * @param ObjectManager $manager
     */
    public function loadPurchasedData(ObjectManager $manager)
    {
        $fileHandler = fopen($this->purchasedFixtureFile, 'r');
        fgetcsv($fileHandler, 1000, ',');
        while (($line = fgetcsv($fileHandler, 1000, ',')) !== false) {
            $product = new Purchased();
            $product->setUserId($line[0]);
            $product->setProductSku($line[1]);
            $manager->persist($product);
        }
        $manager->flush();

        fclose($fileHandler);
    }
}
