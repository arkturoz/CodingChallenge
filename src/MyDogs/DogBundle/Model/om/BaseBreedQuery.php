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
use MyDogs\DogBundle\Model\Breed;
use MyDogs\DogBundle\Model\BreedPeer;
use MyDogs\DogBundle\Model\BreedQuery;
use MyDogs\DogBundle\Model\Dog;

/**
 * @method BreedQuery orderById($order = Criteria::ASC) Order by the id column
 * @method BreedQuery orderByBreed($order = Criteria::ASC) Order by the breed column
 * @method BreedQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method BreedQuery groupById() Group by the id column
 * @method BreedQuery groupByBreed() Group by the breed column
 * @method BreedQuery groupByDescription() Group by the description column
 *
 * @method BreedQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method BreedQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method BreedQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method BreedQuery leftJoinDog($relationAlias = null) Adds a LEFT JOIN clause to the query using the Dog relation
 * @method BreedQuery rightJoinDog($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Dog relation
 * @method BreedQuery innerJoinDog($relationAlias = null) Adds a INNER JOIN clause to the query using the Dog relation
 *
 * @method Breed findOne(PropelPDO $con = null) Return the first Breed matching the query
 * @method Breed findOneOrCreate(PropelPDO $con = null) Return the first Breed matching the query, or a new Breed object populated from the query conditions when no match is found
 *
 * @method Breed findOneByBreed(string $breed) Return the first Breed filtered by the breed column
 * @method Breed findOneByDescription(string $description) Return the first Breed filtered by the description column
 *
 * @method array findById(int $id) Return Breed objects filtered by the id column
 * @method array findByBreed(string $breed) Return Breed objects filtered by the breed column
 * @method array findByDescription(string $description) Return Breed objects filtered by the description column
 */
abstract class BaseBreedQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseBreedQuery object.
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
            $modelName = 'MyDogs\\DogBundle\\Model\\Breed';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new BreedQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   BreedQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return BreedQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof BreedQuery) {
            return $criteria;
        }
        $query = new BreedQuery(null, null, $modelAlias);

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
     * @return   Breed|Breed[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = BreedPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(BreedPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Breed A model object, or null if the key is not found
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
     * @return                 Breed A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `breed`, `description` FROM `breed` WHERE `id` = :p0';
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
            $obj = new Breed();
            $obj->hydrate($row);
            BreedPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Breed|Breed[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Breed[]|mixed the list of results, formatted by the current formatter
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
     * @return BreedQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BreedPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return BreedQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BreedPeer::ID, $keys, Criteria::IN);
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
     * @return BreedQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(BreedPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(BreedPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BreedPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the breed column
     *
     * Example usage:
     * <code>
     * $query->filterByBreed('fooValue');   // WHERE breed = 'fooValue'
     * $query->filterByBreed('%fooValue%'); // WHERE breed LIKE '%fooValue%'
     * </code>
     *
     * @param     string $breed The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BreedQuery The current query, for fluid interface
     */
    public function filterByBreed($breed = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($breed)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $breed)) {
                $breed = str_replace('*', '%', $breed);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(BreedPeer::BREED, $breed, $comparison);
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
     * @return BreedQuery The current query, for fluid interface
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

        return $this->addUsingAlias(BreedPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related Dog object
     *
     * @param   Dog|PropelObjectCollection $dog  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 BreedQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByDog($dog, $comparison = null)
    {
        if ($dog instanceof Dog) {
            return $this
                ->addUsingAlias(BreedPeer::ID, $dog->getBreedId(), $comparison);
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
     * @return BreedQuery The current query, for fluid interface
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
     * @param   Breed $breed Object to remove from the list of results
     *
     * @return BreedQuery The current query, for fluid interface
     */
    public function prune($breed = null)
    {
        if ($breed) {
            $this->addUsingAlias(BreedPeer::ID, $breed->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
