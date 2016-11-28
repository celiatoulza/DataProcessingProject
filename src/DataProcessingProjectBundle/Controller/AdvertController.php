<?php

namespace DataProcessingProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use DataProcessingProjectBundle\Entity\Advert;
use DataProcessingProjectBundle\Entity\Image;
use DataProcessingProjectBundle\Entity\AdvertActivity;
use DataProcessingProjectBundle\Entity\Application;

use DataProcessingProjectBundle\Form\AdvertType;
use DataProcessingProjectBundle\Form\AdvertEditType;
use DataProcessingProjectBundle\Form\ApplicationType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdvertController extends Controller {

	// *******************************************************
	// Cette fonction permet d'afficher la page d'accueil
	// On y représente les différentes annonces de clubs de vacances de la chaîne
	// Elle prend en paramètre la page d'annonces à afficher car celles-ci peuvent s'étaler sur plusieurs pages

	public function indexAction( $page ){
		
		$limit=3;

		if ( $page < 1 ){
			throw new NotFoundHttpException( 'Page "'.$page.'" does not exist.' );
		}

		$listAdverts = $this
					->getDoctrine()
					->getManager()
					->getRepository( "DataProcessingProjectBundle:Advert" )
					->getAdverts( $page, $limit );

		if ( $listAdverts === null ){
			throw new NotFoundHttpException("The page you are looking for does not exist.");
		}

		//nombre totale d'annonces trouvées divisés par le nombre total de pages
		// ceil permet d'arrondir au nombre supérie aka on a 1.33 -> il nous faut 2 pages
		$nbTotalPages = ceil(count( $listAdverts )/$limit) ;


		return $this->render ( 'DataProcessingProjectBundle:Advert:index.html.twig', array( 'listAdverts' => $listAdverts,
						  'nbTotalPages' => $nbTotalPages,
						  'currentPage' => $page ) );
	}


	// *******************************************************
	// Cette fonction permet d'afficher la vue d'une annonce en particulier
	// elle prend en paramètre l'id de l'annonce

	public function viewAction( $id ){

		// on récupère l'EntityManager
		$em = $this->getDoctrine()->getManager();

		// on récupère l'annonce avec l'id en paramètre avec la méthode de récupération de base fournie par Doctrine
		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )->find( $id );

		// on vérifie que l'annonce existe
		if ( $advert === null ){
			throw new NotFoundHttpException("The advert that you are looking for does not exist.");
		}

		// on récupère les 3 dernières candidatures à cette annonce
		// Pour cela, on récupère toutes les applications dont l'advert est l'advert qu'on parle, on les trie par date décroissante et on en prend 3 à partir de 0, comme elles sont dans l'autre sens, on a bien les 3 denières
		$listApplications = $em->getRepository( 'DataProcessingProjectBundle:Application' )
							   ->findBy( 
							   			array( 'advert' => $advert ),
						    			array( 'applicationDate' => 'desc' ), 
						    			3,
						    			0
    								);


		// on récupère la liste des activités pour cette annonce
		$listAdvertActivities = $em->getRepository( 'DataProcessingProjectBundle:AdvertActivity' )->findBy( array( 'advert' => $advert ) );

		// on récupère le nombre d'applications
		$nbApplications = $advert->getNbApplications();

		echo "fin du vue";
		// On affiche la vue correspondante avec les paramètres récupérées. 
		return $this->render( 'DataProcessingProjectBundle:Advert:view.html.twig', array( 'advert' =>$advert, 
					 'listApplications' => $listApplications, 
					 'listAdvertActivities' => $listAdvertActivities, 
					 'nbApplications' => $nbApplications ));
	}


	// *******************************************************
	// Cette fonction nous permet d'ajouter une annonce

	public function addAction( Request $request ){

		$this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page, too bad');

		// on crée un objet Advert
		$advert = new Advert;

		// On crée le formulaire à partir de la variable $advert 
		$form = $this->createForm( AdvertType::class, $advert );

		// On fait le lien Requête <-> Formulaire
		// A partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur.
		$form->handleRequest( $request );


		// On vérifie que le formulaire a été correctement complété
		// et on pérennise l'annonce dans la base de données.	
		if ( $form->isValid() ){

			echo $advert->getCategories();

			$em=$this->getDoctrine()->getManager();
			$em->persist( $advert );
			$em->flush();

			//$request->getSession()->getFlashBag()->add( 'notice', 'Advert added.' );

			// on redirige vers la page de visualisation de l'article
			return $this->redirectToRoute( 'data_processing_project_view', array( 'id' => $advert->getId() ) );
		}


		// si la requête n'est pas un POST alors on affiche le formulaire
		// On passe la méthode createView du formulaire à la vue afin qu'elle puisse afficher le formulaire
		return $this->render( 'DataProcessingProjectBundle:Advert:add.html.twig', array( 'form' => $form->createView() ));

	}

	// *******************************************************
	// Cette fonction permet d'éditer une annonce
	// Elle prend en paramètre l'id de cette annonce

	public function editAction( $id, Request $request ){

		// on récupère l'EntityManager
		$em= $this->getDoctrine()->getManager();

		// on récupère l'annone à modifier
		$advert= $em->getRepository( 'DataProcessingProjectBundle:Advert' )->find( $id );

		// si elle n'existe pas, on renvoie une erreur
		if ( $advert === null ){
			throw new NotFoundHttpException( "The advert you are looking for does not exist" ); 
		}

		// On crée le form
		$form = $this->createForm( AdvertEditType::class, $advert );
	
		// l'utilisateur a soumis le formulaire 
		// la fonction handleRequest permet de faire le lien entre ce que l'utilisateur a inscrit dans le formulaire et l'objet form correspondant de Symfony
		if( $form->handleRequest( $request )->isValid() ) {

			$request->getSession()->getFlashBag()->add( 'notice', 'Advert modified' );


			$em->flush();
			// on redirige sur la vue de cette annonce
			return $this->redirectToRoute( 'data_processing_project_view', array( 'id' => $advert->getId() ) );
		}

		// sinon on affiche le formulaire
		return $this->render( 'DataProcessingProjectBundle:Advert:edit.html.twig', array( 'advert' => $advert, 
					 'form' => $form->createView() 
					  ));

	}

	// *******************************************************
	// Cette fonction nous permet de supprimer une annonce
	// Elle prend en paramètre l'idée de l'annonce à supprimer

	public function deleteAction( $id, Request $request ){

		// On récupère l'EntityManager
		$em = $this->getDoctrine()->getManager();

		// on récupère l'annonce à supprimer
		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )->find( $id );

		// on renvoie une erreur si cette annonce n'existe pas
		if( null === $advert ){
			throw new NotFoundHttpException( "The advert you are looking for does not exist.");
		}

		// on crée un formulaire pour avoir bien une page de confirmation qui va devenir un Post et permettre de passer dans l'autre après
		$form = $this->createFormBuilder()->getForm();

		// si la requête est un post, on supprimer l'annonce
		if ( $form->handleRequest( $request )->isValid() ){
			$em->remove( $advert );
			$em->flush();

			$request->getSession()->getFlashBag()->add( 'info', 'Advert deleted' );

			// on redirige vers l'accueil
			return $this->redirect( $this->generateUrl( 'data_processing_project_home' ) );
		}

	
		// si la requête est un GET, on affiche une page de confirmation avant le delete
		return $this->render( 'DataProcessingProjectBundle:Advert:delete.html.twig', array( 
							'advert' => $advert,
							'form' => $form->createView()
							 ));
		
	}

	// *******************************************************
	// Cette fonction permet de faire une recherche à partir du titre de l'annonce

	public function searchAction( Request $request ){
		
		// on crée le formulaire de recherche nous même avec seulement 2 attributs 
		// le nom pour la recherche par nom et le boutton search qui va nous faire revenir sur cette fonction 
		$form = $this->createFormBuilder( )
						->add( 'title', TextType::class )
						->add( 'search', SubmitType::class)
						->getForm()
						;

		// on fait corréler les attributs de l'objet form de Symfony avec les valeurs entrées dans le form par l'utilisateur
		$form->handleRequest( $request );

		// si le form a été soumis et qu'il est valide
		if( $form->isSubmitted() && $form->isValid() ){

			// on récupère les données entrées dans le formulaire sous forme de tableau 
			$data = $form->getData();

			// on récupère l'EntityManager
			$em= $this->getDoctrine()->getManager();

			// on trouve l'advert correspondante avec le nom entré dans le formulaire
			$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )->findOneBy( array( 'title' => $data[ 'title' ] ));

			// on check que l'utilisateur n'ait pas écrit n'importe quoi
			if ( empty( $advert ) ){
				
				// si jamais l'advert n'existe pas 
				return $this->render( 'DataProcessingProjectBundle:Advert:notExistAdvert.html.twig' );
			} else{
				
					// sinon on redirige sur l'advert en question 
					return $this->redirectToRoute( 'data_processing_project_view', array( 'id' => $advert->getId() ));
			}
		}

		// la première passe : on dirige sur le formulaire ce qui va permettre de compléter la variable $form avec ce que l'utilisateur va entrer
    	return $this->render( 'DataProcessingProjectBundle:Advert:search.html.twig', array(
    			'form' => $form->createView() ) );
  	}


	// *******************************************************
	// Cette fonction permet de rajouter le menu sur le côté contenant les dernières annonces

	public function menuAction( $limit =3 ){

    $listAdverts = $this
    		->getDoctrine()
    		->getManager()
    		->getRepository( 'DataProcessingProjectBundle:Advert' )
    		->findBy(
    			array(), 
    			array( 'date' => 'desc' ), 
    			$limit,
    			0
    			);


    return $this->render( 'DataProcessingProjectBundle:Advert:menu.html.twig', array( 'listAdverts' => $listAdverts ) );
	}


}