<?php

namespace DataProcessingProjectBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DataProcessingProjectBundle\Entity\Activity;

class LoadActivity implements FixtureInterface{

	public function load( ObjectManager $manager ){

		$names = array(
			'rollerskating',
			'resting',
			'hicking',
			'skiing',
			'boat trip',
			'visit a local factory',
			'going to the zoo'
			);

		foreach( $names as $name ){

			$activity = new Activity();
			$activity->setName( $name );

			$manager->persist( $activity );
		}

		$manager->flush();
	}
}