<?php

namespace AppBundle\Service\Job;

use AppBundle\Entity\Job;
use AppBundle\Entity\Token;

class JobReviewMailer
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $moderatorEmail;

    /**
     * @var string
     */
    private $mailerSender;

    /**
     * JobReviewMailer constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer     $swift
     */
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer, $moderatorEmail, $mailerSender)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->moderatorEmail = $moderatorEmail;
        $this->mailerSender = $mailerSender;
    }

    /**
     * @param Job   $job
     * @param Token $oneUseToken
     */
    public function sendModeratorNotification(Job $job, Token $token)
    {
        $content = $this->twig->render('@App/Mail/moderation_request.html.twig', [
            'job' => $job,
            'expirableToken' => $token,
        ]);

        $message = $this->createMessage(
            $this->moderatorEmail,
            'Moderation Request',
            $content
        );

        $this->mailer->send($message);
    }

    public function sendJobAuthorPendingNotification(Job $job)
    {
        $content = $this->twig->render('@App/Mail/pending_job_notification.html.twig', [
            'job' => $job,
        ]);

        $message = $this->createMessage(
            $job->getEmail(),
            'Job Approval pending',
            $content
        );

        $this->mailer->send($message);
    }

    /**
     * @param $to
     * @param $subject
     * @param $renderedTemplate
     *
     * @return \Swift_Message
     */
    private function createMessage($to, $subject, $renderedTemplate)
    {
        $message = \Swift_Message::newInstance(
            $subject,
            $renderedTemplate,
            'text/html'
        );

        $message->setFrom($this->mailerSender);
        $message->setTo($to);

        return $message;
    }
}
