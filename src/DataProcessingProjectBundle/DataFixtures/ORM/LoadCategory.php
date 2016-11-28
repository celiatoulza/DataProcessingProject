<?php

namespace DataProcessingProjectBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DataProcessingProjectBundle\Entity\Category;

class LoadCategory implements FixtureInterface{

	public function load( ObjectManager $manager ){

		$names = array(
			'Sea',
			'Mountain',
			'City',
			'Countryside'
			);

		foreach( $names as $name ){

			$category = new Category();
			$category->setName( $name );

			$manager->persist( $category );
		}

		$manager->flush();
	}
}