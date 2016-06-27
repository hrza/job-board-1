<?php

namespace AppBundle\Service\Job;

use AppBundle\Entity\Job;
use AppBundle\Service\Token\TokenGenerator;

class JobManager
{
    /**
     * @var JobRepository
     */
    private $jobRepository;
    /**
     * @var JobReviewer
     */
    private $jobReviewer;
    /**
     * @var JobReviewMailer
     */
    private $jobReviewMailer;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * JobManager constructor.
     *
     * @param JobRepository   $jobRepository
     * @param JobReviewer     $jobReviewer
     * @param JobReviewMailer $jobReviewMailer
     * @param TokenGenerator  $tokenGenerator
     */
    public function __construct(JobRepository $jobRepository, JobReviewer $jobReviewer, JobReviewMailer $jobReviewMailer, TokenGenerator $tokenGenerator)
    {
        $this->jobRepository = $jobRepository;
        $this->jobReviewer = $jobReviewer;
        $this->jobReviewMailer = $jobReviewMailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param Job $job
     */
    public function approve(Job $job)
    {
        $this->jobRepository->save($job->approve());
    }

    /**
     * @param Job $job
     */
    public function deny(Job $job)
    {
        $this->jobRepository->save($job->deny());
    }

    /**
     * @param Job $job
     */
    public function save(Job $job)
    {
        $this->jobReviewer->review($job);
        $this->jobRepository->save($job);

        if ($job->isPending()) {
            $token = $this->tokenGenerator->generate($job);

            $this->jobReviewMailer->sendJobAuthorPendingNotification($job);
            $this->jobReviewMailer->sendModeratorNotification($job, $token);
        }
    }
}
