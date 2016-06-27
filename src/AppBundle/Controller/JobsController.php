<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use AppBundle\Form\Type\NewJobType;
use AppBundle\Service\Job\JobManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/jobs")
 */
class JobsController extends BaseController
{
    /**
     * @Route("/", methods={"GET"}, name="jobs")
     * @Template("@App/Jobs/index.html.twig")
     */
    public function indexAction()
    {
        return [
            'jobs' => $this->repository()->all(),
        ];
    }

    /**
     * @Route("/new", methods={"GET", "POST"}, name="post_job")
     * @Template("@App/Jobs/new.html.twig")
     */
    public function postJobAction()
    {
        $form = $this->createForm(NewJobType::class);

        return $this->submit($form, function (Job $job) {

            $this->manager()->save($job);

            return $this->redirectToRoute('jobs');
        });
    }

    /**
     * @Route("/{job}/approve", methods={"GET"}, name="approve_job")
     */
    public function approveJobAction(Job $job)
    {
        $this->denyAccessUnlessGranted('edit', $job);

        $this->manager()->approve($job);

        return $this->redirectToRoute('jobs');
    }

    /**
     * @Route("/{job}/deny", methods={"GET"}, name="deny_job")
     */
    public function denyJobAction(Job $job)
    {
        $this->denyAccessUnlessGranted('edit', $job);

        $this->manager()->deny($job);

        return $this->redirectToRoute('jobs');
    }

    /**
     * @return JobManager
     */
    private function manager()
    {
        return $this->get('job_manager');
    }

    /**
     * @return \AppBundle\Service\Job\JobRepository
     */
    private function repository()
    {
        return $this->get('job_repository');
    }
}
