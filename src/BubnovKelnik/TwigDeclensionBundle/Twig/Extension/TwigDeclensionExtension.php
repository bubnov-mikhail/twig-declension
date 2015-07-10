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
     * Cached Declension
     * @var Array 
     */
    private $cached;

    /**
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->cached = [];
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
        
        if($declension = $this->getDeclension($infinitive)){
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

    /**
     * 
     * @param String $infinitive
     * @return Declension | null
     */
    private function getDeclension($infinitive = '')
    {
        if($declension = $this->getCached($infinitive)){
            return $declension;
        }

        $repository = $this->em->getRepository('BubnovKelnikTwigDeclensionBundle:Declension');
        if($declension = $repository->findOneByInfinitive($infinitive)){
            $this->setCached($declension);
            
            return $declension;
        }

        return null;
    }
    
    /**
     * 
     * @param String $infinitive
     * @return Declension | null
     */
    private function getCached($infinitive = '') {
        $infinitive = mb_strtolower($infinitive, 'UTF-8');
        if(isset($this->cached[md5($infinitive)])){
            return $this->cached[md5($infinitive)];
        }
        
        return null;
    }
    
    /**
     * 
     * @param Declension $declension
     * @return \BubnovKelnik\TwigDeclensionBundle\Twig\Extension\TwigDeclensionExtension
     */
    private function setCached(Declension $declension) {
        $this->cached[md5(mb_strtolower($declension->getInfinitive(), 'UTF-8'))] = $declension;
        
        return $this;
    }
}