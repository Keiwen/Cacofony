<?php

namespace Keiwen\Cacofony\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseEasyAdminController;
use Symfony\Component\HttpFoundation\Response;

class EasyAdminController extends BaseEasyAdminController
{


    /**
     * new focus action: list 'child' entities with same 'parent' (one-to-many relation)
     * @return Response
     */
    protected function focusAction()
    {
        $this->setFocusDqlFilter('list');
        $this->entity['templates']['list'] = '@KeiwenCacofony/admin/focus.html.twig';
        return parent::listAction();
    }


    /**
     * override search action to include search from focus
     * @return Response
     */
    protected function searchAction()
    {
        $this->setFocusDqlFilter('search');
        return parent::searchAction();
    }


    /**
     * complete existing dql filter to add the focus on parent entity
     * @param string $action
     */
    protected function setFocusDqlFilter(string $action)
    {
        $fromMap = strtolower($this->request->query->get('focusFromMap'));
        $fromId = $this->request->query->getInt('focusFromId');
        if (!empty($fromMap) && !empty($fromId)) {
            // if focus set, add dql_filter
            $dqlFilter = "entity.$fromMap = $fromId";
            $this->entity[$action]['dql_filter'] = empty($this->entity[$action]['dql_filter']) ? $dqlFilter : $dqlFilter . ' AND (' . $this->entity[$action]['dql_filter'] . ')';
        }
    }


    /**
     * after creating new entity, try to automatically assign parent entity if focus set
     * @return object
     */
    protected function createNewEntity()
    {
        $entity = parent::createNewEntity();

        $targetEntity = $this->getFocusedEntity();
        if (!empty($targetEntity)) {
            // if focus found, set association by default
            $fromMap = strtolower($this->request->query->get('focusFromMap'));
            $setTargetMethod = 'set' . $fromMap;
            if (method_exists($entity, $setTargetMethod)) {
                $entity->{$setTargetMethod}($targetEntity);
            }
        }
        return $entity;
    }


    /**
     * get 'parent' entity if focus set
     * @return object|null
     */
    protected function getFocusedEntity()
    {
        $fromEntity = strtolower($this->request->query->get('focusFromEntity'));
        $fromId = $this->request->query->getInt('focusFromId');
        // is there a focus set?
        if (!empty($fromEntity) && !empty($fromId)) {
            // find class in current entity associations
            if(!empty($this->entity['properties'][strtolower($fromEntity)]['targetEntity'])) {
                $targetEntityClass = $this->entity['properties'][strtolower($fromEntity)]['targetEntity'];
                $targetEm = $this->getDoctrine()->getManagerForClass($targetEntityClass);
                // find focused entity
                $targetEntity = $targetEm->find($targetEntityClass, $fromId);
                return $targetEntity;
            }
        }
        return null;
    }

}
