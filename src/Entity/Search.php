<?php

namespace App\Entity;

class Search {
    
    private $field = 'id';
    private $value = '';
    private $orderField = 'id';
    private $order = 'DESC';
    private $limit = 100;
    private $entity = NULL;

    

    /**
     * Get the value of entity
     */ 
    public function getEntity() : ?string
    {
        return $this->entity;
    }

    /**
     * Set the value of entity
     *
     * @return  self
     */ 
    public function setEntity($entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get the value of limit
     */ 
    public function getLimit() : ?int
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit) : self
    {
        $this->limit = $limit;

        return $this;
    }


    /**
     * Get the value of order
     */ 
    public function getOrder() : ?string
    {
        return $this->order;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */ 
    public function setOrder($order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get the value of orderField
     */ 
    public function getOrderField() : ?string
    {
        return $this->orderField;
    }

    /**
     * Set the value of orderField
     *
     * @return  self
     */ 
    public function setOrderField($orderField): self
    {
        $this->orderField = $orderField;

        return $this;
    }

    /**
     * Get the value of value
     */ 
    public function getValue() : ?string
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return  self
     */ 
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of field
     */ 
    public function getField() : ?string
    {
        return $this->field;
    }

    /**
     * Set the value of field
     *
     * @return  self
     */ 
    public function setField($field): self
    {
        $this->field = $field;

        return $this;
    }
}