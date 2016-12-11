<?php

namespace DataProcessingProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use DataProcessingProjectBundle\Entity\Comment;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use DataProcessingProjectBundle\Form\CommentType;


class CommentController extends Controller {

	public function viewCommentsAction( $advertId ){

		$listComments=[];

		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )->find( $advertId );

		$listComments = $em->getRepository( 'DataProcessingProjectBundle:Comment' )->findBy( array( "advert" => $advertId ));

		return $this->render ( 'DataProcessingProjectBundle:Comment:indexComment.html.twig', array( 
									"advert" => $advert,
									"comments" => $listComments ) );
	}

	public function addCommentAction( $advertId, Request $request ){

		$em=$this->getDoctrine()->getManager();
		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )->find( $advertId );

		$comment = new Comment( $this->getUser(), $advert );

		$form = $this->createForm( CommentType::class, $comment );

		$form->handleRequest( $request );


		if( $form->isSubmitted() and $form->isValid() ){

			$em->persist( $comment );
			$em->flush();

			return $this->redirectToRoute( 'data_processing_project_view', array( "id" => $advertId ));

		}

		return $this->render ( 'DataProcessingProjectBundle:Comment:addComment.html.twig', array( 
									"advert" => $advert, 
									"form" => $form->createView() ) );
	}


	public function deleteCommentAction( $advertId, Request $request ){

		$em = $this->getDoctrine()->getManager();

		// on crée notre propre formulaire
		// il n'a que un champ de type EntityType donc en fait une collection 
		// on décide que cette collection soit de type comment et que ça soit le contenu qu'on montre
		$form = $this->createFormBuilder()
					->add( 'comment', EntityType::class, array(
                				'class' => 'DataProcessingProjectBundle:Comment',
                				'choice_label' => 'content',
                				'multiple' => true,
                		))
					->add( 'save', SubmitType::class, array( 'label' => 'Delete' ))
					->getForm()
					;

		$form->handleRequest( $request );

		if ( $form->isValid() && $form->isSubmitted() ){

			$data= $form->getData();

			//var_dump( $data['comment'] );
			foreach( $data['comment'] as $commentToDelete ){
				$em->remove( $commentToDelete );
			}

			$em->flush();

			return $this->redirectToRoute( 'data_processing_project_comments', array( 'advertId' => $advertId ));

		}

		return $this->render ( 'DataProcessingProjectBundle:Comment:deleteComment.html.twig', array(
									"advert" => $advertId, 
									"form" => $form->createView() ) );

	}

}