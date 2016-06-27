<?php

namespace AppBundle\Service\Token;

use AppBundle\Entity\Job;
use AppBundle\Entity\Token;

class TokenGenerator
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * TokenManager constructor.
     *
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param Job $job
     *
     * @return Token
     */
    public function generate(Job $job)
    {
        $token = new Token();
        $token->setJob($job);
        $this->tokenRepository->save($token);

        return $token;
    }
}
