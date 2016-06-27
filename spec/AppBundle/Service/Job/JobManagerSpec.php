<?php

namespace spec\AppBundle\Service\Job;

use AppBundle\Entity\Job;
use AppBundle\Entity\Token;
use AppBundle\Service\Job\JobRepository;
use AppBundle\Service\Job\JobReviewer;
use AppBundle\Service\Job\JobReviewMailer;
use AppBundle\Service\Token\TokenGenerator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JobManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Service\Job\JobManager');
    }

    function let(
        JobRepository $jobRepository,
        JobReviewer $jobReviewer,
        JobReviewMailer $jobReviewMailer,
        TokenGenerator $tokenGenerator
    )
    {
        $this->beConstructedWith(
            $jobRepository,
            $jobReviewer,
            $jobReviewMailer,
            $tokenGenerator
        );
    }

    function it_should_approve_job(Job $job, $jobRepository)
    {
        $job->approve()->shouldBeCalled()->willReturn($job);
        $jobRepository->save($job)->shouldBeCalled();
        $this->approve($job);
    }

    function it_should_deny_job(Job $job, $jobRepository)
    {
        $job->deny()->shouldBeCalled()->willReturn($job);
        $jobRepository->save($job)->shouldBeCalled();
        $this->deny($job);
    }

    function it_should_save_job_and_send_mail(Job $job, Token $token, $jobReviewer, $jobRepository, $tokenGenerator, $jobReviewMailer)
    {

        $job->isPending()->willReturn(true);
        $jobReviewer->review($job)->shouldBeCalled();
        $jobRepository->save($job)->shouldBeCalled();

        $tokenGenerator->generate($job)->shouldBeCalled()->willReturn($token);
        $jobReviewMailer->sendModeratorNotification($job, $token)->shouldBeCalled();
        $jobReviewMailer->sendJobAuthorPendingNotification($job)->shouldBeCalled();

        $this->save($job);
    }

    function it_should_not_send_mail_on_job_save(Job $job, Token $token, $jobReviewer, $jobRepository, $tokenGenerator, $jobReviewMailer)
    {
        $job->isPending()->willReturn(false);
        $jobReviewer->review($job)->shouldBeCalled();
        $jobRepository->save($job)->shouldBeCalled();

        $tokenGenerator->generate($job)->shouldNotBeCalled();
        $jobReviewMailer->sendModeratorNotification($job, $token)->shouldNotBeCalled();
        $jobReviewMailer->sendJobAuthorPendingNotification($job)->shouldNotBeCalled();

        $this->save($job);
    }
}
