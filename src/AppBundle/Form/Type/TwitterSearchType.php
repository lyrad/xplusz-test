<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Entity\TwitterSearch;
use Endroid\Twitter\Twitter;

class TwitterSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    ->add('q', TextType::class, array( 'label' => 'Recent tweets search term :' ))
	    ->add('count', ChoiceType::class, array(
		'label' => 'Number of entries per page :',
    		'choices'  => array(
        		'2' => 2,
        		'4' => 4,
        		'8' => 8,
        		'16' => 16,
        		'32' => 32,
        		'64' => 64,
    		)
	    ))
            ->add('send', SubmitType::class, array( 'label' => 'Send' ))
            ->add('previous', SubmitType::class, array( 'label' => 'Previous' ))
            ->add('next', SubmitType::class, array( 'label' => 'Next' ));
    }
}
