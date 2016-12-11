<?php

namespace DataProcessingProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use DataProcessingProjectBundle\Entity\Comment;

use DataProcessingProjectBundle\Form\CommentType;


class CommentController extends Controller {

	public function viewCommentsAction( $advertId ){

		$listComments=[];

		return $this->render ( 'DataProcessingProjectBundle:Comment:indexComment.html.twig', array( "advert" => $advertId, 
		"comments" => $listComments ) );
	}

	public function addCommentAction( $advertId, Request $request ){

		$em=$this->getDoctrine()->getManager();
		$advert = $em->getRepository( 'DataProcessingProjectBundle:Advert' )->find( $advertId );

		$comment = new Comment( $this->getUser() );

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

}