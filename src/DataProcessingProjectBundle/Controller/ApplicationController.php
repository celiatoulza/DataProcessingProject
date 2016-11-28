<?php

namespace DataProcessingProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use DataProcessingProjectBundle\Entity\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


use DataProcessingProjectBundle\Form\ApplicationType;

class ApplicationController extends Controller {


	// *******************************************************
	// Cette fonction permet de postuler pour l'annonce en question
	// Elle prend en paramètre l'id de l'annonce pour laquelle on candidate

	public function applyAction( $id, Request $request ){

		// on récupère l'EntityManager
		$em =$this->getDoctrine()->getManager();

		//On cherche l'annonce en question
		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )->find( $id );

		// Si l'annonce n'existe pas, on jette une erreur
		if( $advert === null ){
			throw new NotFoundHttpException("Tha advert ".$id." does not exist.");
		}

		// on crée un objet Application
		$application = new Application( $this->getUser() );

		// on crée un formulaire à partir de la variable $application créée
		$form = $this->createForm( ApplicationType::class, $application );

		// on match les valeurs entrées par l'utilisateur dans le formulaire avec notre objet Form de Symfony
		$form->handleRequest( $request );


		// !!! --- JE FAIS LA VERIF CONCERNANT LES CHOIX --- !!! //

		 // on vérifie que le choix entré dans le formulaire courant n'a pas déjà été utilisé par cet utilisateur
		 if( isset( $application->getChoiceNumber() ) ){

		 	// on récupère toutes les autres applications de l'utilisateur en cours
		 	$choisesApplicationsUser = $this->getDoctrine()
		 		->getManager()
		 		->getRepository( 'DataProcessingProjectBundle:Application' )
		 		->findAllChoicesAlreadyChooseByThisUser( $this->getUser() );
		 	
		 	// si le current choice fait déjà partie des choix utilisé de l'utilisateur, on le fait retourner au formulaire de candidature pour qu'ils choisissent un autre choix
		 	if ( in_array( $currentChoice, $choisesApplicationsUser[0] ) ){
		 		echo "deja utilisé ce choix";

		 		return $this->render( 'DataProcessingProjectBundle:Application:apply.html.twig', array(
					'form' => $form->createView(), 
					'advert' => $advert, 
					'errorChoice' => "You have already choose this choice number for your applicaions." ));
		 	}

		 } 


		// si notre formulaire a été soumis, c'est à dire qu'on l'a passé en POST et qu'il est valide
		if( $form->isSubmitted() && $form->isValid() ){

			// ajouter l'annonce à cette nouvelle candidature
			$application->setAdvert( $advert );

			// nouvelle entité crée donc faut la persister
			$em->persist( $application );

			// met à jour la base de données
			$em->flush();

			// on redirige sur la vue une fois que l'on a ajouté notre application
			return $this->redirectToRoute( 'data_processing_project_view', array( 
					'id' => $id ));
		}


		return $this->render( 'DataProcessingProjectBundle:Application:apply.html.twig', array(
					'form' => $form->createView(), 
					'advert' => $advert ));
	}


	// *******************************************************
	// Cette fonction permet de voir toutes les candidatures pour l'annonce en question

	public function allApplicationsAction( $id, Request $request ){

		// on récupère l'EntityManager
		$em= $this->getDoctrine()->getManager();

		// on récupère la liste des candidatures pour cette annonce
		// on obtient une liste d'objets ou un tableau d'objets ? 
		$listApplications= $em->getRepository( 'DataProcessingProjectBundle:Application' )
							  ->findByAdvert( $id )
							  ;
		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )
					 ->find( $id );

		// Ce n'est pas un formulaire, juste un affichage

		return $this->render( 'DataProcessingProjectBundle:Application:allApplications.html.twig', array(
					'listApplications' => $listApplications,
					'advert' => $advert ));
	}

	// *******************************************************
	// Cette fonction permet aux développeur de voir une candidature en particulier
	// Elle prend en paramètre l'id de la candidature

	public function viewApplicationAction( $id ){

		$em = $this->getDoctrine()->getManager();

		$application = $em->getRepository( 'DataProcessingProjectBundle:Application' )->find( $id );

		return $this->render( 'DataProcessingProjectBundle:Application:viewApplication.html.twig', array(
				'application' => $application ));
	}

	// *******************************************************
	// Cette action permet aux développeurs de supprimer une candidature en particulier
	// Elle prend en paramètre l'id de la candidature

	public function deleteApplicationAction( $id, Request $request ){

		$em = $this->getDoctrine()->getManager();
		$application = $em->getRepository( 'DataProcessingProjectBundle:Application' )->find( $id );

		// on crée un form pour créer la page de confirmation 
		// en effet quand va confirmer la suppression, le form va devenir valide et être en post
		$form = $this->createFormBuilder()->getForm();

		if ( $form->handleRequest( $request )->isValid() ){

			$em->remove( $application );
			$em->flush();

			return $this->redirectToRoute( 'data_processing_project_all_applications', array(
					'id' => $application->getAdvert()->getId() ));
		}

		return $this->render( 'DataProcessingProjectBundle:Application:deleteApplication.html.twig', array(
				'application' => $application, 
				'form' => $form->createView() ));
	}




}