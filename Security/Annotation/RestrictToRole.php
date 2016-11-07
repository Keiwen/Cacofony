<?php

namespace Keiwen\Cacofony\Security\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * The RestrictToRole class handles the Security annotation checking roles.
 *
 * @Annotation
 */
class RestrictToRole extends Security
{

    protected $rolePrefix = array('ROLE_');
    protected $roleList = array();
    protected $associationOperator = self::OPERATOR_OR;

    const OPERATOR_OR = 'or';
    const OPERATOR_AND = 'and';

    /**
     * Use annotation by giving list of role as parameter.
     * List roles names (with or without 'ROLE_' prefix) that can access
     * to method, in a string, separated by comma or semicolon
     * (with or without space)
     * Role name are not case sensitive
     * example: "USER, ROLE_ADMIN;anonymous"
     * @param string $roleList contains list of role
     */
    public function setValue(string $roleList)
    {
        $this->roleList = $this->explodeInput($roleList);

    }


    /**
     * @inheritdoc
     */
    public function getExpression()
    {
        $roleList = $this->roleList;
        foreach($roleList as &$role) {
            //ensure to have a valid role prefix
            $this->validateRolePrefix($role);
            //build has_role expression
            $role = "has_role('$role')";
        }
        return implode(" $this->associationOperator ", $roleList);
    }


    /**
     * @deprecated
     * @param string $expression
     */
    public function setExpression($expression)
    {
    }


    /**
     * List role prefixes if other than ROLE_
     * (with or without trailing underscore)
     * Similar rules than value with list roles
     * example: additionalRolePrefix="PREFIX"
     * @param string $prefixes
     */
    public function setAdditionalRolePrefix(string $prefixes)
    {
        $prefixes = $this->explodeInput($prefixes);
        foreach($prefixes as $prefix) {
            //be sure to end with _
            $this->rolePrefix[] = rtrim($prefix, '_') . '_';
        }
    }


    /**
     * By default, allow access if user have one of listed roles
     * Fill this value to allow only if user have all listed roles
     * @param boolean|mixed $all
     */
    public function setMustHaveAll(bool $all)
    {
        if($all) $this->associationOperator = self::OPERATOR_AND;
    }


    /**
     * @param string $input
     * @param bool   $toUpper
     * @return array
     */
    protected function explodeInput(string $input, bool $toUpper = true)
    {
        //remove spaces
        $input = str_replace(' ', '', $input);
        //harmonize separator
        $input = str_replace(',', ';', $input);
        //to upper?
        if($toUpper) $input = strtoupper($input);
        //explode
        return explode(';', $input);
    }


    /**
     * @param string $role
     * @return bool
     */
    protected function hasValidRolePrefix(string $role)
    {
        foreach($this->rolePrefix as $prefix) {
            if(strpos($role, $prefix) === 0) return true;
        }
        return false;
    }

    /**
     * @param string $role
     */
    protected function validateRolePrefix(string &$role)
    {
        if(!$this->hasValidRolePrefix($role)) {
            $defaultPrefix = reset($this->rolePrefix);
            $role = $defaultPrefix . $role;
        }
    }

}