<?php

namespace MyDogs\DogBundle\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use MyDogs\DogBundle\Model\Dog;
use MyDogs\DogBundle\Model\Race;
use MyDogs\DogBundle\Model\RacePeer;
use MyDogs\DogBundle\Model\RaceQuery;

/**
 * @method RaceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method RaceQuery orderByRace($order = Criteria::ASC) Order by the race column
 * @method RaceQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method RaceQuery groupById() Group by the id column
 * @method RaceQuery groupByRace() Group by the race column
 * @method RaceQuery groupByDescription() Group by the description column
 *
 * @method RaceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method RaceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method RaceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method RaceQuery leftJoinDog($relationAlias = null) Adds a LEFT JOIN clause to the query using the Dog relation
 * @method RaceQuery rightJoinDog($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Dog relation
 * @method RaceQuery innerJoinDog($relationAlias = null) Adds a INNER JOIN clause to the query using the Dog relation
 *
 * @method Race findOne(PropelPDO $con = null) Return the first Race matching the query
 * @method Race findOneOrCreate(PropelPDO $con = null) Return the first Race matching the query, or a new Race object populated from the query conditions when no match is found
 *
 * @method Race findOneByRace(string $race) Return the first Race filtered by the race column
 * @method Race findOneByDescription(string $description) Return the first Race filtered by the description column
 *
 * @method array findById(int $id) Return Race objects filtered by the id column
 * @method array findByRace(string $race) Return Race objects filtered by the race column
 * @method array findByDescription(string $description) Return Race objects filtered by the description column
 */
abstract class BaseRaceQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseRaceQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'MyDogs\\DogBundle\\Model\\Race';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RaceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   RaceQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RaceQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RaceQuery) {
            return $criteria;
        }
        $query = new RaceQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Race|Race[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RacePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RacePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Race A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Race A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `race`, `description` FROM `race` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Race();
            $obj->hydrate($row);
            RacePeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Race|Race[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Race[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return RaceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RacePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RaceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RacePeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RaceQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(RacePeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(RacePeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RacePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the race column
     *
     * Example usage:
     * <code>
     * $query->filterByRace('fooValue');   // WHERE race = 'fooValue'
     * $query->filterByRace('%fooValue%'); // WHERE race LIKE '%fooValue%'
     * </code>
     *
     * @param     string $race The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RaceQuery The current query, for fluid interface
     */
    public function filterByRace($race = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($race)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $race)) {
                $race = str_replace('*', '%', $race);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RacePeer::RACE, $race, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RaceQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RacePeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related Dog object
     *
     * @param   Dog|PropelObjectCollection $dog  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RaceQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByDog($dog, $comparison = null)
    {
        if ($dog instanceof Dog) {
            return $this
                ->addUsingAlias(RacePeer::ID, $dog->getRaceId(), $comparison);
        } elseif ($dog instanceof PropelObjectCollection) {
            return $this
                ->useDogQuery()
                ->filterByPrimaryKeys($dog->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDog() only accepts arguments of type Dog or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Dog relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RaceQuery The current query, for fluid interface
     */
    public function joinDog($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Dog');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Dog');
        }

        return $this;
    }

    /**
     * Use the Dog relation Dog object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \MyDogs\DogBundle\Model\DogQuery A secondary query class using the current class as primary query
     */
    public function useDogQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinDog($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Dog', '\MyDogs\DogBundle\Model\DogQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Race $race Object to remove from the list of results
     *
     * @return RaceQuery The current query, for fluid interface
     */
    public function prune($race = null)
    {
        if ($race) {
            $this->addUsingAlias(RacePeer::ID, $race->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
