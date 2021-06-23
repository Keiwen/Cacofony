<?php

namespace Keiwen\Cacofony\Security;


use Keiwen\Cacofony\Security\Annotation\RestrictToRole;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Security;

class RoleChecker
{

    protected $security;
    protected $rolePrefixes = array();

    public function __construct(Security $security, array $rolePrefixes = array())
    {
        $this->security = $security;
        $this->rolePrefixes = $rolePrefixes;
    }

    /**
     * same rules than annotation
     * @see RestrictToRole
     * @param string|array $roleList contains list of roles
     * @param bool         $mustHaveAll
     * @return bool
     */
    public function hasRole($roleList, $mustHaveAll = false)
    {
        if(is_array($roleList)) {
            $roleList = implode(',', $roleList);
        }
        $values = array(
            'value' => $roleList,
            'additionalRolePrefix' => implode(',', $this->rolePrefixes),
            'mustHaveAll' => $mustHaveAll,
        );
        $anotation = new RestrictToRole($values);
        $expression = $anotation->getExpression();
        try {
            return $this->security->isGranted(new Expression($expression));
        } catch(AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }


}
