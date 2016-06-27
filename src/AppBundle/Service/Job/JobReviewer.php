<?php

namespace AppBundle\Service\Job;

use AppBundle\Entity\Job;

class JobReviewer
{
    /**
     * @var JobRepository
     */
    private $jobRepository;

    /**
     * JobReviewer constructor.
     *
     * @param JobRepository $jobRepository
     */
    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    /**
     * @param Job $job
     */
    public function review(Job $job)
    {
        if (0 != $this->jobRepository->approvedJobsCount($job->getEmail())) {
            return $job->approve();
        }

        return $job->pending();
    }
}
