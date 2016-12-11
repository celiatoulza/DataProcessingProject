<?php

namespace DataProcessingProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use DataProcessingProjectBundle\Entity\Advert;
use DataProcessingProjectBundle\Entity\Category;

use DataProcessingProjectBundle\Form\CategoryType;

class CategoryController extends Controller {

	public function indexCategoryAction( $advertId ){

		$em = $this->getDoctrine()->getManager();

		$advert = $em->getRepository( "DataProcessingProjectBundle:Advert" )->find( $advertId ) ;

		return $this->render( "DataProcessingProjectBundle:Category:indexCategory.html.twig", array(
						"advert" => $advert ));

	}


	public function addCategoryAction( $advertId, Request $request ){

		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository( "DataProcessingProjectBundle:Advert" )->find( $advertId );


		$category = new Category();
		$form = $this->createForm( CategoryType::class, $category );

		$form->handleRequest( $request );

		if( $form->isSubmitted() && $form->isValid() ){

			// on check si la catégorie existe pas déjà
			$check = $em->getRepository( "DataProcessingProjectBundle:Category" )->findBy( array( "name" => $category->getName() ) );

			#var_dump($check);

			if( !empty( $check )){

				$advert->addCategory ( $check[0] );
				$em->flush();

			}

			else{

				$em->persist( $category );
				$advert->addCategory ( $category );

				$em->flush();
			}
		

			return $this->redirectToRoute( 'data_processing_project_category', array( 'advertId' => $advertId ));
		}

		return $this->render( "DataProcessingProjectBundle:Category:addCategory.html.twig", array(
						"advert" => $advert, 
						"form" => $form->createView() ));

	}
}
