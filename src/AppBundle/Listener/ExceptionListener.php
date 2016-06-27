<?php

namespace AppBundle\Listener;

use AppBundle\Security\Authentication\Token\TemporaryToken;
use AppBundle\Service\Token\TokenRepository;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ExceptionListener
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * ExceptionListener constructor.
     *
     * @param TokenRepository $tokenRepository
     * @param TokenStorage    $tokenStorage
     */
    public function __construct(TokenRepository $tokenRepository, TokenStorage $tokenStorage)
    {
        $this->tokenRepository = $tokenRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /** @var TemporaryToken $token */
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TemporaryToken) {
            return;
        }

        $this->tokenRepository->save($token->getUser()->renew());
    }
}
