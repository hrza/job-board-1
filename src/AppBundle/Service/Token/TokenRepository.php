<?php

namespace AppBundle\Service\Token;

use AppBundle\Entity\Token;
use Doctrine\ORM\EntityManager;

class TokenRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TokenRepository constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $id
     *
     * @return Token
     */
    public function findById($id)
    {
        return $this->doctrineRepo()->findOneBy([
            'id' => $id,
            'used' => false,
        ]);
    }

    private function doctrineRepo()
    {
        return $this->entityManager->getRepository(Token::class);
    }

    /**
     * @param Token $token
     */
    public function save(Token $token)
    {
        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }
}
