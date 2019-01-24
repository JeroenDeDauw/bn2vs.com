<?php

declare( strict_types = 1 );

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig_Error_Runtime;

class ExceptionListener {

	private $twig;

	public function __construct( \Twig_Environment $twig ) {
		$this->twig = $twig;
	}

	public function onKernelException( GetResponseForExceptionEvent $event ) {
//		if ( $event->getException() instanceof Twig_Error_Runtime ) {
//			$event->setResponse( new RedirectResponse( '/' ) );
//		}
		if ( $event->getException() instanceof NotFoundHttpException ) {
			$response = new Response(
				$this->twig->render( 'errors/404.html.twig' ),
				404
			);

			$event->setResponse( $response );
		}
	}
}