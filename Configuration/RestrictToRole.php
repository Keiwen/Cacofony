<?php

namespace Keiwen\Cacofony\Configuration;


/**
 * The RestrictToRole class handles the Security attribute checking roles.
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class RestrictToRole
{

    protected $rolePrefix = array('ROLE_');
    protected $roleList = array();
    protected $associationOperator = self::OPERATOR_OR;

    public const OPERATOR_OR = 'or';
    public const OPERATOR_AND = 'and';

    /**
     * Use attribute by giving list of role as parameter.
     * List roles names (with or without 'ROLE_' prefix) that can access
     * to method, in a string, separated by comma or semicolon
     * (with or without space)
     * Role name are not case sensitive
     * example: "USER, ROLE_ADMIN;anonymous"
     *
     * @param string $roles contains list of role
     * @param bool $mustHaveAll default false, user allowed if have one of listed roles. Set true to allow only if all listed roles
     * @param array $additionalRolePrefixes list of role prefixes other than 'ROLE_', with or without trailing underscore
     */
    public function __construct(string $roles, bool $mustHaveAll = false, array $additionalRolePrefixes = array())
    {
        $this->roleList = $this->explodeInput($roles);
        $this->associationOperator = $mustHaveAll ? self::OPERATOR_AND : self::OPERATOR_OR;
        foreach($additionalRolePrefixes as $prefix) {
            if(empty($prefix)) continue;
            //be sure to end with _
            $this->rolePrefix[] = rtrim($prefix, '_') . '_';
        }
    }


    public function getExpression()
    {
        $roleList = $this->roleList;
        foreach($roleList as &$role) {
            //ensure to have a valid role prefix
            $this->validateRolePrefix($role);
            //build has_role expression
            $role = "is_granted('$role')";
        }
        return implode(" $this->associationOperator ", $roleList);
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
