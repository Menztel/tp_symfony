<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BannedUserListener
{
    private Security $security;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        $request = $event->getRequest();
        
        // Check if user is banned and not already on the banned page
        if ($user && in_array('ROLE_BANNED', $user->getRoles()) && 
            $request->getPathInfo() !== '/banned' && 
            $request->getPathInfo() !== '/logout') {
            
            $response = new RedirectResponse($this->urlGenerator->generate('app_banned'));
            $event->setResponse($response);
        }
    }
}
