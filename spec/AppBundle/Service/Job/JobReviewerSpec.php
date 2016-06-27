<?php

namespace spec\AppBundle\Service\Job;

use AppBundle\Entity\Job;
use AppBundle\Service\Job\JobRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JobReviewerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Service\Job\JobReviewer');
    }

    function let(JobRepository $jobRepository)
    {
        $this->beConstructedWith($jobRepository);
    }

    function it_should_approve_job(Job $job, $jobRepository)
    {
        $email = 'someone@someone.com';

        $job->getEmail()->willReturn($email);

        $jobRepository->approvedJobsCount($email)->shouldBeCalled()->willReturn(1);

        $job->approve()->shouldBeCalled()->willReturn($job);

        $this->review($job);
    }

    function it_should_not_approve_job(Job $job, $jobRepository)
    {
        $email = 'someone@someone.com';

        $job->getEmail()->willReturn($email);

        $jobRepository->approvedJobsCount($email)->shouldBeCalled()->willReturn(0);

        $job->pending()->shouldBeCalled()->willReturn($job);

        $this->review($job);
    }
}
