<?php

namespace AppBundle\Service\Job;

use AppBundle\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;

class JobRepository
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * JobRepository constructor.
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param $id
     *
     * @return Job|null
     */
    public function findById($id)
    {
        return $this->doctrineRepository()->find($id);
    }

    public function approvedJobsCount($email)
    {
        $qb = $this->doctrineRepository()->createQueryBuilder('job');

        $query = $qb
            ->select('COUNT(job.id)')
            ->where('job.email = :email')
            ->andWhere('job.status = :status')
            ->setParameters([
                'email' => $email,
                'status' => Job::APPROVED,
            ])
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->doctrineRepository()->findAll();
    }

    /**
     * @param Job $job
     */
    public function save(Job $job)
    {
        $this->manager()->persist($job);
        $this->manager()->flush();
    }

    /**
     * @return \Doctrine\ORM\EntityManager|object
     */
    private function manager()
    {
        return $this->registry->getEntityManager();
    }

    /**
     * @return EntityRepository
     */
    private function doctrineRepository()
    {
        return $this->manager()->getRepository(Job::class);
    }
}
