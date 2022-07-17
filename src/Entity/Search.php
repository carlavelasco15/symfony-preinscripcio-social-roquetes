<?php

namespace App\Entity;

class Search
{
    
    private $field = 'id';
    private $value = '';
    private $orderField = 'id';
    private $order = 'ASC';
    private $limit = 100;
    private $entity = NULL;
    private $entityId = NULL;

    
    public function getEntity() : ?string
    {
        return $this->entity;
    }

 
    public function setEntity($entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getEntityId() 
    /* : ?Participant */
    {
        return $this->entityId;
    }

 
    public function setEntityId($entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }



    public function getLimit() : ?int
    {
        return $this->limit;
    }


    public function setLimit($limit) : self
    {
        $this->limit = $limit;

        return $this;
    }


    public function getOrder() : ?string
    {
        return $this->order;
    }


    public function setOrder($order): self
    {
        $this->order = $order;

        return $this;
    }


    public function getOrderField() : ?string
    {
        return $this->orderField;
    }


    public function setOrderField($orderField): self
    {
        $this->orderField = $orderField;

        return $this;
    }


    public function getValue() : ?string
    {
        return $this->value;
    }


    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }


    public function getField() : ?string
    {
        return $this->field;
    }

 
    public function setField($field): self
    {
        $this->field = $field;

        return $this;
    }
}