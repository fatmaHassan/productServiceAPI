<?php

namespace App\Repository;

use App\Entity\Purchased;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Purchased|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchased|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchased[]    findAll()
 * @method Purchased[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasedRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchased::class);
    }

    public function deleteAllUserPurchaseBySKU(int $userId, string $sku)
    {
        try {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        DELETE FROM purchased p
        WHERE p.user_id = :userId
        AND product_sku = :productsku
        ';

            $stmt = $conn->prepare($sql);
            $stmt->execute(['userId' => $userId, 'productsku'=> $sku]);
        } catch (Exception $e) {


        }

    }

    public function findUserProducts(int $userId): array{
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT p.sku,p.name FROM purchased pu
        inner join product p 
        on pu.product_sku = p.sku
        WHERE pu.user_id = :userId
        ORDER BY pu.id ASC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['userId' => $userId]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }
}
