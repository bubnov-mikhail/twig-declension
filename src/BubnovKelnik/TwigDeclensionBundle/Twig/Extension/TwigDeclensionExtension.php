<?php

namespace BubnovKelnik\TwigDeclensionBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use BubnovKelnik\TwigDeclensionBundle\Entity\Declension;

class TwigDeclensionExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('declension', [$this, 'onDeclension']),
        ];
    }
    
    /**
     * 
     * @param string $infinitive
     * @return string
     */
    public function onDeclension($infinitive = '', $form = Declension::INFINITIVE, $count = null) {
        if(empty($infinitive)){
            return $infinitive;
        }
        
        $repository = $this->em->getRepository('BubnovKelnikTwigDeclensionBundle:Declension');
        if($declension = $repository->findOneByInfinitive($infinitive)){
            $form = $declension->getForm($form, $count);
            
            return $this->fixCase($infinitive, $form);
        }

        return $infinitive;
    }
    
    /**
     * Make Uppercase for first letter in $form if case of the first letter in $infinitive is also Upper
     * @param string $infinitive
     * @param string $form
     * @return string
     */
    private function fixCase($infinitive = '', $form = '') {
        return ucfirst(strtolower($infinitive)) == $infinitive 
            ? self::my_mb_ucfirst($form)
            : $form
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'declension_extension';
    }
    
    /**
     * 
     * @param String $str
     * @return String
     */
    static private function my_mb_ucfirst($str, $encode = 'UTF-8') {
        $fc = mb_strtoupper(mb_substr($str, 0, 1, $encode), $encode);
        
        return $fc.mb_substr($str, 1 , mb_strlen($str), $encode);
    }

}