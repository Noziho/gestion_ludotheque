<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param string $title
     * @param int $id
     * @return float|int|mixed|string
     * Search an item by collection and Title
     */
    public function searchItemByTitle (string $title, int $id)
    {
        $query = $this->createQueryBuilder('i');
        if ($title && $id){
            $query->andWhere('i.collections = ' . $id . 'AND i.title LIKE :title')
                ->setParameter('title', '%' . $title . '%')
            ;
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param string $editor
     * @param int $id
     * @return float|int|mixed|string
     *  Search an item by collection and Editor
     */
    public function searchItemByEditor (string $editor, int $id)
    {
        $query = $this->createQueryBuilder('i');
        if ($editor && $id){
            $query->andWhere('i.collections = ' . $id . 'AND i.editor LIKE :editor')
                ->setParameter('editor', '%' . $editor . '%')
            ;
        }
        return $query->getQuery()->getResult();
    }
}
