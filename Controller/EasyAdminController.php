<?php

namespace Keiwen\Cacofony\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseEasyAdminController;

class EasyAdminController extends BaseEasyAdminController
{


    protected function focusAction()
    {
        $fromEntity = strtolower($this->request->query->get('focusFromEntity'));
        $fromId = $this->request->query->getInt('focusFromId');
        if (!empty($fromEntity) && !empty($fromId)) {
            // if focus set, add dql_filter
            $dqlFilter = "entity.$fromEntity = $fromId";
            $this->entity['list']['dql_filter'] = empty($this->entity['list']['dql_filter']) ? $dqlFilter : $dqlFilter . ' AND (' . $this->entity['list']['dql_filter'] . ')';
            // set custom template
            $this->entity['templates']['list'] = '@KeiwenCacofony/admin/focus.html.twig';
        }
        return parent::listAction();
    }


    protected function createNewEntity()
    {
        $entity = parent::createNewEntity();

        $targetEntity = $this->getFocusedEntity();
        if (!empty($targetEntity)) {
            // if focus found, set association by default
            $fromEntity = strtolower($this->request->query->get('focusFromEntity'));
            $setTargetMethod = 'set' . $fromEntity;
            if (method_exists($entity, $setTargetMethod)) {
                $entity->{$setTargetMethod}($targetEntity);
            }
        }
        return $entity;
    }


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
