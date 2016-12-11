<?php

namespace DataProcessingProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use UserBundle\Form\UserType;


class AccountController extends Controller {


	public function indexAction(){

		$user = $this->getUser();
		
		$em=$this->getDoctrine()->getManager();
		$listApplications = $em->getRepository( 'DataProcessingProjectBundle:Application')->findBy( array( 'username' => $user->getId() ));

		$listAdverts= [];
		if ( !(empty($listApplications ))){
			foreach( $listApplications as $application ){
				$titre = $em->getRepository( 'DataProcessingProjectBundle:Advert')->find( $application->getAdvert()->getId() )->getTitle();

				# dans un tableau, on associe l'id de la candidature au titre de l'annonce pour laquelle est faite cette candidature
				$listAdverts[ $application->getId() ] = $titre;
			} 
		}


		return $this->render ( 'DataProcessingProjectBundle:Account:index.html.twig', array( 
									'applications' => $listApplications, 
									'adverts' => $listAdverts ) );
	}


	public function editInformationsAction( Request $request ){

		$user = $this->getUser();

		$form = $this->createForm( UserType::class, $user );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ){

			$request->getSession()->getFlashBag()->add( 'notice', 'Personnal Informations modified' );

			$encoder = $this->container->get( 'security.password_encoder' );
			$encoded = $encoder->encodePassword( $user, $user->getPassword() );
			$user->setPassword( $encoded );

			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirectToRoute( 'data_processing_project_home' );
		}

		return $this->render( 'DataProcessingProjectBundle:Account:editPersonnalInformations.html.twig', array(
								'form' => $form->createView() ));

	}
}