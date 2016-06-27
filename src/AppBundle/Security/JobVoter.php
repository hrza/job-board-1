<?php

namespace AppBundle\Security;

use AppBundle\Entity\Job;
use AppBundle\Entity\Token;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class JobVoter extends Voter
{
    const EDIT = 'edit';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        return $attribute === self::EDIT && $subject instanceof Job;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $singleUseToken = $token->getUser();

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($singleUseToken, $subject);
        }

        return false;
    }

    private function canEdit(Token $singleUseToken, Job $job)
    {
        return $singleUseToken->getJob() && $singleUseToken->getJob()->getId() == $job->getId();
    }
}
