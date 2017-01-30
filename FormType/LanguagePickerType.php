<?php

namespace Keiwen\Cacofony\FormType;


use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LanguagePickerType extends ChoiceType
{

    protected $languageKeys = array();


    /**
     * LanguagePickerType constructor.
     * To be declared as service
     *
     * @param array $languageKeys available languages (can be overridden in form options)
     */
    public function __construct(array $languageKeys = array())
    {
        parent::__construct(null);
        $this->languageKeys = $languageKeys;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        //add options to override language keys in form option
        $resolver->setDefined('language_keys');
        //add options to translate languages to user locale or original language name
        $resolver->setDefined('translate_languages');
        $resolver->setDefaults(array(
            //default: keep names in user locale
            'translate_languages' => true,
            //default language keys match parameters given in service declaration
            'language_keys' => $this->languageKeys,
            //do not try to translate languages names
            'choice_translation_domain' => false,
        ));
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(empty($options['choices'])) {
            //consider that choices are not overwritten
            //build languages names choices with translate option
            $options['choices'] = $this->getLanguagesChoices($options['language_keys'], !empty($options['translate_languages']));
        }
        parent::buildForm($builder, $options);
    }


    /**
     * @param array $languageKeys
     * @param bool  $userLocale true (default) to get languages names translated to user locale, false for original names
     * @return array
     */
    protected function getLanguagesChoices(array $languageKeys, bool $userLocale = true)
    {
        $lgBundle = Intl::getLanguageBundle();
        if(empty($languageKeys)) {
            //set all languages
            $languages = $lgBundle->getLanguageNames();
            //bundle return array with key => name, so return it directly if user locale asked
            if($userLocale) return array_flip($languages);
            //else just keep keys, need to translate names
            $languageKeys = array_keys($languages);
        }

        //here we should have sequential arrays with countries keys
        $choices = array();
        foreach($languageKeys as $lk) {
            //get name in target language or use user locale
            $locale = $userLocale ? null : $lk;
            $region = null;
            //if _ found, region is precised as well
            if(strpos($lk, '_') !== false) {
                list($lk, $region) = explode('_', $lk, 2);
            }
            $name = $lgBundle->getLanguageName($lk, $region, $locale);
            if($name) $choices[$name] = $lk;
        }
        return $choices;
    }

}