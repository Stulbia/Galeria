<?php

/**
 * AccessDeniedListener.
 *
 * Klasa nasłuchująca na wyjątki w aplikacji Symfony.
 * i przekierowuje użytkownika
 */

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * AccessDeniedListener.
 *
 * Klasa nasłuchująca na wyjątki w aplikacji Symfony.
 */
class AccessDeniedListener
{
    // Router Symfony, używany do generowania URL-i
    private $router;

    // RequestStack, używany do pobierania aktualnego żądania
    private $requestStack;

    /**
     * Konstruktor klasy.
     *
     * @param RouterInterface $router
     * @param RequestStack    $requestStack
     */
    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * Metoda obsługująca wyjątki.
     *
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        // Pobranie wyjątku
        $exception = $event->getThrowable();

        // Sprawdzenie, czy wyjątek jest typu AccessDeniedException, NotFoundHttpException lub ResourceNotFoundException
        if (!$exception instanceof AccessDeniedException &&
            !$exception instanceof NotFoundHttpException &&
            !$exception instanceof AccessDeniedHttpException &&
            !$exception instanceof ResourceNotFoundException) {
            return;
        }

        // Pobranie aktualnego żądania
        $request = $this->requestStack->getCurrentRequest();

        // Pobranie nagłówka 'referer'
        $referer = $request->headers->get('referer');

        // Jeżeli referer jest dostępny, przekierowanie na niego
        if ($referer) {
            $response = new RedirectResponse($referer);
        } else {
            // Jeżeli referer nie jest dostępny, przekierowanie na domyślną trasę
            $response = new RedirectResponse($this->router->generate('default_route'));
        }

        // Ustawienie odpowiedzi w evencie
        $event->setResponse($response);
    }
}
