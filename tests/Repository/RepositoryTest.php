<?php
use PHPUnit\Framework\TestCase;
use App\Entity\Purchased;
use App\Entity\User;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
class RepositoryTest  extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @dataProvider tablesProvider
     */


    public function testTableIsNotEmpty($entityClass) : void
    {
        $records = $this->entityManager
            ->getRepository($entityClass)
            ->findAll()
        ;

        $this->assertIsIterable($records);
    }

    public function tablesProvider(): array
    {
        return [
            [User::class],
            [Purchased::class],
            [Product::class]
        ];
    }
    public function testFetchUserProducts()
    {
        $products = $this->entityManager
            ->getRepository(Purchased::class)
            ->findUserProducts(1)
        ;

        $this->assertIsIterable($products);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}