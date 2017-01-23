<?php

namespace Keiwen\Cacofony\FormType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguagePickerType extends AbstractType
{

    protected $languages = array();


    /**
     * LanguagePickerType constructor.
     * To be declared as service
     *
     * @param array $languages available languages
     * @param bool  $userLocale true to display languages names in user locale
     */
    public function __construct(array $languages = array(), bool $userLocale = false)
    {
        $lgBundle = Intl::getLanguageBundle();
        foreach($languages as $language) {
            //get name in target language or use user locale
            $locale = $userLocale ? null : $language;
            $region = null;
            //if _ found, region is precised as well
            if(strpos($language, '_') !== false) {
                list($language, $region) = explode('_', $language, 2);
            }
            $name = $lgBundle->getLanguageName($language, $region, $locale);
            if($name) $this->languages[$name] = $language;
        }
    }


    public function getParent()
    {
        return ChoiceType::class;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->languages,
            //do not try to translate languages names
            'choice_translation_domain' => false,
        ));
    }



}