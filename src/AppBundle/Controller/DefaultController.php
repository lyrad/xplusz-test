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

		// If next button is clicked and next max id stored in session
		if ($form->get('next')->isClicked() && $session->has('next_max_id')) {
			$query_param = array(
                                'q' => $form->get('q')->getData(),
                                'count' => $form->get('count')->getData(),
				'max_id' => $session->get('next_max_id')
                        );
		// If previous button is clicked
		} else if($form->get('previous')->isClicked()) {
			$query_param = array(
                                'q' => $form->get('q')->getData(),
                                'count' => $form->get('count')->getData(),
				'max_id' => $session->get('prev_max_id')
                        );
			var_dump($session->get('prev_max_id'));
		// If send button is clicked
		} else {
			$session->remove('next_max_id');
			$session->remove('prev_max_id');
			$query_param = array(
                        	'q' => $form->get('q')->getData(),
                        	'count' => $form->get('count')->getData(),
                	);
		}

		// Query Twitter API & get the response
		$endroidtwitter = $this->get('endroid.twitter');
		$response = $endroidtwitter->query('search/tweets', 'GET', 'json', $query_param);
		$tweets = json_decode($response->getContent(), true);

		// If max_id/since id exists in the response, update form fields
		if( true === isset($tweets['search_metadata']['next_results']) ) {
			$next_query_param = [];
			parse_str(parse_url($tweets['search_metadata']['next_results'], PHP_URL_QUERY), $next_query_param);
			if( true === isset($next_query_param['max_id']) && true === is_numeric($next_query_param['max_id']) ) {
				$session->set('next_max_id', $next_query_param['max_id']);
				$session->set('prev_max_id', $tweets['search_metadata']['max_id_str']);
				var_dump($session->get('next_max_id'));
				var_dump($session->get('prev_max_id'));
				$disableNext = false;
				$disablePrev = false;
			} else {
				var_dump("no param");
				
			}
		} else {
			// No next result can be found
			$disableNext = true;
			$disablePrev = true;
		}
		
    	} else {
		$tweets = [ 'statuses' => [] ];
	}

	// Abandon ship... Can't make a pagination with twitter api... See debug infos...
	$disableNext = true;
	$disablePrev = true;

	// Return template
	return $this->render(
                'default/index.html.twig',
                array( 
			'form' => $form->createView(),
			'tweets' => $tweets,
			'disableNext' => $disableNext,
			'disablePrev' => $disablePrev,
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
