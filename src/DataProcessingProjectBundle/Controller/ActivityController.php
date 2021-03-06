<?php

namespace DataProcessingProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


use DataProcessingProjectBundle\Entity\Activity;
use DataProcessingProjectBundle\Entity\AdvertActivity;
use DataProcessingProjectBundle\Entity\ActivityCategory;

use DataProcessingProjectBundle\Form\ActivityType;
use DataProcessingProjectBundle\Form\AdvertActivityType;
use DataProcessingProjectBundle\Form\AdvertPreexistingActivityType;
use DataProcessingProjectBundle\Form\ActivityCategoryType;

use DataProcessingProjectBundle\Repository\AdvertActivityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;




use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ActivityController extends Controller {


	//**********************************************************
	// Cette fonction affiche l'ensemble des activités d'une annonce
	// Elle prend en paramètre l'id de l'annonce

	public function indexAction( $advertId ){

		$em = $this->getDoctrine()->getManager();

		$advert = $em->getRepository( "DataProcessingProjectBundle:Advert")->find( $advertId );

		$listAdvertActivities = $em->getRepository( 'DataProcessingProjectBundle:AdvertActivity' )
							->findBy( 
								array( 'advert' => $advertId ),
								null, 
								null, 
								null)
							;

		//$listAllActivities = $em->getRepository( "DataProcessingProjectBundle:Activity")->findAll();


		return $this->render( 'DataProcessingProjectBundle:Activity:indexActivities.html.twig', array(
				'advert' => $advert, 
				'listAdvertActivities' => $listAdvertActivities, 
				//'listAllActivities' => $listAllActivities;
		));
	}


	//**********************************************************
	// Cette fonction permet d'ajouter une activité à une annonce
	// Elle prend en paramètre l'id de l'annonce

	public function addActivityAction( $advertId, Request $request ){

		// on crée un entitiymanager
		$em = $this->getDoctrine()->getManager();

		// on récupère l'advert
		$advert = $em->getRepository( "DataProcessingProjectBundle:Advert" )->find( $advertId );

		// on crée l'activty
		$activity = new Activity();
		// on crée l'advertActivty
		$advertActivity = new AdvertActivity();

		// on crée un formulaire qui est basé sur le Activity form 
		$form = $this->createForm( AdvertActivityType::class, $advertActivity );

		// on met à jour la var $advert avec les données entrées par le 
		$form->handleRequest( $request );

		// on vérifie que le formulaire ont été soumis et validés
		if ( $form->isSubmitted() && $form->isValid() ){

			// on persiste en premier l'activité ce qui va nous permettre d'avoir un id dans $advertActivity->getId()
			$em->persist( $advertActivity->getActivity() );

			// on met l'advert dans advertActivity
			$advertActivity->setAdvert( $advert );

			// on persiste l'advertactivity parce qu'elle existe pas encore
			$em->persist( $advertActivity );

			// on met à jour la base de données
			$em->flush();

			// on met ensuite à jour le prix moyen de l'activité donc après avoir à jour la base de données avec notre nouvelle activité et donc nouvelle advertactivité également
			$price= $em->getRepository( 'DataProcessingProjectBundle:AdvertActivity' )->findAveragePrice( $advertActivity->getActivity()->getId() )[0][1];

			$advertActivity->getActivity()->setAveragePrice( intval($price) );

			$em->flush();

			return $this->redirectToRoute( 'data_processing_project_index_activities', array( 'advertId' => $advertId ));
			
		}

		// Lors de la première passe, on envoie sur la page du formulaire
		return $this->render( 'DataProcessingProjectBundle:Activity:addActivity.html.twig', array(
				'advert' => $advertId, 
				'form' => $form->createView(), 
		));
	}

	//**********************************************************
	// Cette fonction permet de visualiser l'activité propre d'une annonce.
	// Elle prend en paramètres l'id de l'activité

	public function viewActivityAction( $id, $advertId ){

		//on récup l'EntityManager
		$em= $this->getDoctrine()->getManager();

		// on récupère d'abord l'activité correspondant à cet id 
		$activity = $em->getRepository( 'DataProcessingProjectBundle:Activity')->find( $id );

		// on récupère d'abord l'annonce correspondant à cet AdvertId 
		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert')->find( $advertId );

		// on récupère un tableau d'advertactivité qui ont cette activité et on a donc accès à l'advert et à l'activité 
		// on récupère un tableau tête de banane
		$advertActivity = $em->getRepository( "DataProcessingProjectBundle:AdvertActivity" )
							->findBy(
								array( "activity" => $activity, 
										"advert" => $advert ),
								null, 
								null, 
								null );

		// on vérifie qu'on récup bien que une seule valeur
		if ( count( $advertActivity )  >1) {

			throw new NotFountHttpException("Il y a soucis parce que vous avez plus d'un résultat, or pour une annonce, chaque activité doit avoir un nom différent.");
		}

		// on récup les annonces qui ont cette activité
		$listAdverts= $this->getDoctrine()
						   ->getManager()
						   ->getRepository( 'DataProcessingProjectBundle:AdvertActivity' )
						   ->findAllAdvertForThisActivity( $activity->getName() )
						 ;

		/*foreach( $listAdverts as $test ){
			var_dump( $test['title'] );
		}*/



		//var_dump($advertActivity);
		return $this->render( 'DataProcessingProjectBundle:Activity:viewActivity.html.twig', array( "advertActivity" => $advertActivity, 'listAdverts' => $listAdverts ) );
	}

	//**********************************************************
	// Cette fonction permet d'ajouter une activité qui existe déjà dans la base de donnée, c'est à dire une activité qui est déjà présente dans une autre destination
	// Elle prend en paramètre l'id de l'advert à laquelle on veut ajouter l'activité 

	public function addPreexistingActivityAction( $advertId, Request $request){

		// on crée un entitiymanager
		$em = $this->getDoctrine()->getManager();

		// on récupère l'advert à laquelle on veut ajouter l'activité
		$advert = $em->getRepository( "DataProcessingProjectBundle:Advert" )->find( $advertId );

		// on crée l'advertActivity
		$advertActivity = new AdvertActivity();

		// on crée un formulaire qui est basé sur le Activity form 
		$form = $this->createForm( AdvertPreexistingActivityType::class, $advertActivity );

		// on met à jour la var $advert avec les données entrées par l'utilisateur
		$form->handleRequest( $request );

		// on vérifie que le formulaire a été soumis et validé
		if ( $form->isSubmitted() && $form->isValid() ){

			// ON RECUPERE UNE PUTAIN DE COLLECTION DANS $advertActivity->getActivity()
			$activity = $em->getRepository( "DataProcessingProjectBundle:Activity" )->findOneBy( array ( "id" => $advertActivity->getActivity()[0]->getId() ) );

			$advertActivity->setActivity( $activity );
			
			// on met l'advert dans advertActivity
			$advertActivity->setAdvert( $advert );

			// on persiste l'activity parce qu'elle existe pas encore
			$em->persist( $advertActivity );

			// on met à jour la base de données
			$em->flush();

			return $this->redirectToRoute( 'data_processing_project_index_activities', array( 'advertId' => $advertId ));			
		}

		// Lors de la première passe, on envoie sur la page du formulaire
		return $this->render( 'DataProcessingProjectBundle:Activity:addExistingActivity.html.twig', array(
				'advert' => $advertId, 
				'form' => $form->createView(), 
		));

	}

	//**********************************************************
	// Cette fonction permet d'éditer une activité propre à une annonce, seulement si on change les attributs propres à l'activité comme son nom, ceux-ci vont être modifiés dans toutes les annonces. 
	// Elle prend en paramètre l'id de l'activité qui a donc déjà été crée et l'id de l'annonce

	public function editActivityAction( $id, $advertId, Request $request ){

		// on crée un entitiymanager
		$em = $this->getDoctrine()->getManager();

		// on récupère l'advert
		$advert = $em->getRepository( "DataProcessingProjectBundle:Advert" )->find( $advertId );

		// on crée l'activity
		$activity = $em->getRepository( "DataProcessingProjectBundle:Activity" )->find( $id );

		// on crée l'advertActivity => TABLEAU
		$advertActivity = $em->getRepository( "DataProcessingProjectBundle:AdvertActivity" )
							->findBy( array( 'advert' => $advert,
											'activity' => $activity
										) );

		if ( count($advertActivity) >1 ){
			throw new NotFountHttpException( "Erreur : vous avez plus d'un couple avec ces ids advert-activity" );
		}

		// on crée un formulaire qui est basé sur le Activity form 
		$form = $this->createForm( AdvertActivityType::class, $advertActivity[0] );

		// on met à jour la var $advert avec les données entrées par le 
		$form->handleRequest( $request );

		// on vérifie que le formulaire ont été soumis et validés
		if ( $form->isSubmitted() && $form->isValid() ){

			// on met à jour la base de données
			$em->flush();

			return $this->redirectToRoute( 'data_processing_project_index_activities', array( 'advertId' => $advertId ));
			
		}

		// Lors de la première passe, on envoie sur la page du formulaire
		return $this->render( 'DataProcessingProjectBundle:Activity:addActivity.html.twig', array(
				'advert' => $advertId, 
				'form' => $form->createView(), 
		));

	}

	//**********************************************************
	// Cette fonction permet de supprimer une activité propre à une annonce, mais elle restera présente si elle était associée à d'autre annonce
	// Elle prend en paramètre l'id de l'activité et l'id de l'annonce à laquelle l'activité doit être supprimée

	public function deleteActivityAction( $id, $advertId, Request $request ){

		$em= $this->getDoctrine()->getManager();

		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )->find( $advertId );

		$activity = $em->getRepository( 'DataProcessingProjectBundle:Activity' )->find( $id );

		$advertActivity = $em->getRepository( 'DataProcessingProjectBundle:AdvertActivity' )->findBy( array( 'advert' => $advert, 'activity' => $activity ));

		if ( count($advertActivity) >1 ){
			throw new NotFountHttpException( "Erreur : vous avez plus d'un couple avec ces ids advert-activity" );
		}

		$form = $this->createFormBuilder()->getForm();

		$form->handleRequest( $request );

		if ( $form->isValid() ){

			$em->remove( $advertActivity[0] );

			$em->flush();

			return $this->redirectToRoute( 'data_processing_project_index_activities', array( 'advertId' => $advertId ));

		}

		return $this->render( 'DataProcessingProjectBundle:Activity:deleteActivity.html.twig', array( 'advertActivity' => $advertActivity[0], 'form' => $form->createView() ));
		
	}


	//**********************************************************
	// Cette fonction permet de rechercher une activité par une categorie

	public function searchActivityAction( Request $request ){

		//$category = new ActivityCategory();
		//$form = $this->createForm( ActivityCategoryType::class, $activity );
		
		$form = $this->createFormBuilder()
					->add( 'categories', EntityType::class, array(
                				'class' => 'DataProcessingProjectBundle:ActivityCategory',
                				'choice_label' => 'name',
                				'multiple' => true,
                		))
					->add( 'save', SubmitType::class, array( 'label' => 'Search' ))
					->getForm()
					;

		// on récupère l'activityCategory d'abord (pour l'instant, on a que potentiellement le nom)
		//$category = $em->getRepository( "DataProcessingProjectBundle:ActivityCategory" )->findByName( $data['category'] );


		$form->handleRequest( $request );

		if( $form->isSubmitted() && $form->isValid() ){

			// on récupère les données entrées dans le formulaire sous forme de tableau 
			$data = $form->getData();
			//$test = $data['categories'][0];
			//echo $test.getName();


			$categoryNameSelected = $data['categories'][0]->getName() ;

			$em = $this->getDoctrine()->getManager();
			$category = $em->getRepository( "DataProcessingProjectBundle:ActivityCategory" )->findByName( $categoryNameSelected );

			//var_dump( $category );

			// WOUAH 3h de perdu pour ça 
			$activities = $em->getRepository( "DataProcessingProjectBundle:Activity" )->findAllActivitiesForACategory( $category[0]->getId() );

			$advertsActivity =[];
			if( !(empty( $activities ))){
				foreach( $activities as $activity ){
					$advertsActivity[ $activity->getId() ] = $em->getRepository( 'DataProcessingProjectBundle:AdvertActivity' )->findAdvertsByActivity( $activity->getId() );
					// var_dump( $advertsActivity[ $activity->getId() ]);
				}
			}


			// je fais un appel à la base de données pour chaque activité  pour trouver le prix moyen de celle-ci trouvée du coup très très mauvais
			/*foreach( $activities as $activity ){
				$priceActivity[ $activity->getId() ] = $em->getRepository( 'DataProcessingProjectBundle:AdvertActivity' )->findAveragePrice( $activity->getId() )[0][1];
			}*/

			//var_dump( $activities );

			// ensuite on récupère toutes les activités qui font partie de cette catégorie.
			//$activities = $em->getRepository( "DataProcessingProjectBundle:Activity" )->findBy( array( 'categories' => 3 ) );

			//findAllActivitiesForThisCategory( $category[0]->getId(0) );
			//findBy( array( 'categories' => $category[0]->getId() ) );


			return $this->render( 'DataProcessingProjectBundle:Activity:displaySearchActivityByCategory.html.twig', array(
					"category" => $categoryNameSelected,
					"activities" => $activities, 
					"advertsActivity" =>$advertsActivity,
					//"priceActivity" => $priceActivity
					 ));

		}

		return $this->render( 'DataProcessingProjectBundle:Activity:searchActivity.html.twig', array(
				"form" => $form->createView() ) );
	}
	
}