<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use UserBundle\Form\UserType;
use UserBundle\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class SecurityController extends Controller
{
	public function loginAction( Request $request )
	{

	    $authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    

	    return $this->render('UserBundle:Security:login.html.twig', array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
	       
	    ));
	}


	//**********************************************************
	// Cette fonction permet aux utilisateurs de s'inscrire et donc pour nous d'être présent en base de données.

	public function registrationAction( Request $request ){

		// on crée un utilisateur vide
		$user = new User();

		// on crée un formulaire à partir de l'entité User et de la variable user que l'on vient de créer pour avoir ensuite accès aux valeurs entrées par l'utilisateur dans le formulaire
		$form = $this->createForm( UserType::class, $user );

		// c'est à ce moment que notre variable $user va récupérer dans ses attributs les données rentrées par l'utilisateur
		$form->handleRequest( $request );

		// on charge l'EntityManager
		$em= $this->getDoctrine()->getManager();


		// on vérifie si il n'y a pas déjà un utilisateur avec ce même pseudo
		$userVerif = $em->getRepository( 'UserBundle:User' )
				   ->findOneBy(array("username" => $user->getUsername() ));

		
		// si il y a déjà un utilisateur avec ce pseudo, renvoie une page d'erreur pour l'instant
		if( $userVerif !== null ){
			throw new NotFoundHttpException( "This pseudo is already taken ... too late ma poule" ); 
			//return new Response( $user->getId() );
		}

		// si il  y a pas d'utilisateur avec ce pseudo, on est good
		// on vérifie si $user a été complété
		if( $form->isSubmitted() && $form->isValid() ){

			$user->setRoles( 'ROLE_USER' );			

			$encoder = $this->container->get( 'security.password_encoder' );
			$encoded = $encoder->encodePassword( $user, $user->getPassword() );
			$user->setPassword( $encoded );
			
			//si l'inscription est good, on persiste $user parce que c'est une nouvelle entrée et du coup symfony la connait pas encore
			$em->persist( $user );

			// on met à jour la base de données
			$em->flush();	

			// on redirige l'utilisateur pour qu'ils se log in
			return $this->RedirectToRoute( 'login', array(
					'registration' => true,
			) );
		}	
		
		// on renvoie le formulaire pour que l'utilisateur complète les champs qu'on va pouvoir réucpérer ensuite
		return $this->render( 'UserBundle:Security:registration.html.twig', array(
				'form' => $form->createView() ) );
	
	}

}
