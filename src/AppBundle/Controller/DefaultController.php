<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
	$twitter = $this->get('endroid.twitter');

	// Retrieve the user's timeline
	// $tweets = $twitter->getTimeline(array(
	//    'count' => 5
	// ));

	// Or retrieve the timeline using the generic query method
	$response = $twitter->query('search/tweets', 'GET', 'json', array( 'q' => 'obama' ));
	$tweets = json_decode($response->getContent());
	var_dump($tweets);

	return $this->render(
        	'default/index.html.twig',
        	array()
    	);
    }
}
