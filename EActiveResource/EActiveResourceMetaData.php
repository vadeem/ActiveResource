<?php
/**
 * @author Johannes "Haensel" Bauer
 * @since version 0.1
 */
/**
 * This is the ActiveResource version of the CActiveMetaData class. It is used by ActiveResources to define
 * vital parameters for a RESTful communication between Yii and the service.
 */
class EActiveResourceMetaData
{

    public $properties;     //The properties of the resource according to the schema configuration
    public $relations=array();
    
    public $attributeDefaults=array();
    
    public $schema;

    private $_model;

    public function __construct($model)
    {
            $this->_model=$model;

            if(($resourceConfig=$model->rest())===null)
                    throw new EActiveResourceException(Yii::t('ext.EActiveResource','The resource "{resource}" configuration could not be found in the activeresource configuration.',array('{resource}'=>get_class($model))));
           
            $this->schema=new EActiveResourceSchema($resourceConfig,$model->properties());
                                                
            $this->properties=$this->schema->properties;

            foreach($this->properties as $name=>$property)
            {
                    if($property->defaultValue!==null)
                            $this->attributeDefaults[$name]=$property->defaultValue;
            }
            
            foreach($model->relations() as $name=>$config)
            {
                    $this->addRelation($name,$config);
            }
    }
    
    /**
    * Adds a relation.
    *
    * $config is an array with three elements:
    * relation type, the related active resource class and the "foreign key".
    *
    * @throws EActiveResourceException
    * @param string $name $name Name of the relation.
    * @param array $config $config Relation parameters.
    * @return void
    */
    public function addRelation($name,$config)
    {
        if(isset($config[0],$config[1],$config[2]))  // relation class, AR class, FK
                $this->relations[$name]=new $config[0]($name,$config[1],$config[2],array_slice($config,3));
        else
                throw new EActiveResourceException(Yii::t('ext.EActiveResource','Active resource "{class}" has an invalid configuration for relation "{relation}". It must specify the relation type, the related active record class and the foreign key.', array('{class}'=>get_class($this->_model),'{relation}'=>$name)));
    }

    /**
    * Checks if there is a relation with specified name defined.
    *
    * @param string $name $name Name of the relation.
    * @return boolean
    */
    public function hasRelation($name)
    {
        return isset($this->relations[$name]);
    }

    /**
    * Deletes a relation with specified name.
    *
    * @param string $name $name
    * @return void
    */
    public function removeRelation($name)
    {
        unset($this->relations[$name]);
    }
    
    public function getSchema()
    {
        return $this->schema;
    }
}

?>
