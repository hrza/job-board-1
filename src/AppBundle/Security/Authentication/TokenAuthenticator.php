<?php

namespace AppBundle\Security\Authentication;

use AppBundle\Entity\Token;
use AppBundle\Security\Authentication\Token\TemporaryToken;
use AppBundle\Security\TokenProvider;
use AppBundle\Service\Token\TokenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class TokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * TokenAuthenticator constructor.
     *
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $tokenProvider, $providerKey)
    {
        if (!$tokenProvider instanceof TokenProvider) {
            throw new \RuntimeException('something terribly wrong');
        }

        $oneUseToken = $tokenProvider->loadUserByUsername($token->getCredentials());

        if (!$oneUseToken) {
            return;
        }

        return new TemporaryToken(
            $oneUseToken,
            $token->getCredentials(),
            $providerKey,
            $oneUseToken->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof TemporaryToken;
    }

    public function createToken(Request $request, $providerKey)
    {
        $tokenId = $this->lookupTokenId($request);

        if (!$tokenId) {
            return;
        }

        return new TemporaryToken('anon', $tokenId, $providerKey, []);
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    private function lookupTokenId(Request $request)
    {
        $tokenId = $request->get('token', null);

        if (!$tokenId) {
            $tokenId = $request->headers->get('token', null);
        }

        return $tokenId;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if (!$token instanceof TemporaryToken) {
            return;
        }

        $singleUseToken = $token->getUser();

        if (!$singleUseToken instanceof Token) {
            return;
        }

        $this->tokenRepository->save($singleUseToken->expire());
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof UsernameNotFoundException) {
            return new Response('Wrong token!', 403);
        }

        return new Response('Error occured', 500);
    }
}
