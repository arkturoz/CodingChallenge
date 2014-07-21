<?php

namespace MyDogs\DogBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'race' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.MyDogs.DogBundle.Model.map
 */
class RaceTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.MyDogs.DogBundle.Model.map.RaceTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('race');
        $this->setPhpName('Race');
        $this->setClassname('MyDogs\\DogBundle\\Model\\Race');
        $this->setPackage('src.MyDogs.DogBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('race', 'Race', 'VARCHAR', false, 100, null);
        $this->getColumn('race', false)->setPrimaryString(true);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Dog', 'MyDogs\\DogBundle\\Model\\Dog', RelationMap::ONE_TO_MANY, array('id' => 'race_id', ), null, null, 'Dogs');
    } // buildRelations()

} // RaceTableMap
