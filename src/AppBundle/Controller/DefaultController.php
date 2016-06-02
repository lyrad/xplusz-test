<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\TwitterSearch;
use AppBundle\Form\Type\TwitterSearchType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
	// Start session
	// $session = new Session();
	// $session->start();
	$session = $request->getSession();

	if( false === $session->has('previous_q') ) {
		$previous_q = array();
		$session->set('previous_q', $previous_q);
	} else {
		$previous_q = $session->get('previous_q');
	}

	// Create TwitterSearch object
	$twittersearch = new TwitterSearch();

	// Create form
	$form = $this->createForm(TwitterSearchType::class, $twittersearch);

	 $form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid()) {
		// Save query in session
		if( false === in_array( $form->get('q')->getData(), $previous_q ) ) {
			$previous_q[] = $form->get('q')->getData();
			$session->set('previous_q', $previous_q);
		}

		$endroidtwitter = $this->get('endroid.twitter');

		$response = $endroidtwitter->query('search/tweets', 'GET', 'json', array(
			'q' => $form->get('q')->getData(),
			'count' => $form->get('count')->getData()
		));
		
		$tweets = json_decode($response->getContent());

    	} else {
		$tweets = [ 'statuses' => [] ];
	}

	return $this->render(
                'default/index.html.twig',
                array( 
			'form' => $form->createView(),
			'tweets' => $tweets,
			'previous_q' => $previous_q
		)
        );	







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
