<?php

namespace Icinga\Module\Customactions\Repository;

use Icinga\Application\Config;
use Exception;
use Icinga\Application\Icinga;
use Icinga\Module\Neteye\Model\BaseModel;
use ipl\Sql\Connection;
use ipl\Sql\Select;
use ipl\Sql\Sql;
use PDO;
use ReflectionException;
use Icinga\Module\Customactions\Utils\SessionUtil;

/**
 * This abstract class helps for database crud operations and fetch data from database
 *
 * Class BaseRepository
 * @package Icinga\Module\Neteye\Repository
 */
abstract class DatabaselessRepository
{
    protected $dbConnection;
    protected $model;
    protected $table;
    protected $moduleName;
    private $icingaRequest;
    public $searchableProperties;
    private $objectType;

    /**
     * BaseRepository constructor.
     * @param null $moduleName
     * @throws ReflectionException
     */
    public function __construct($moduleName = null)
    {
        // When using hooks, the Repository is initialized using the module name which calls the hook, not
        // the one which is providing the hook.
        // If you want to use the module name of the module which provide the hook you must initialize the moduleName.
        if (! is_null($moduleName)) {
            $this->setModuleName($moduleName);
        }
        // $this->connectToDb();
        $this->model = $this->getModelNamespace();
        $this->table = $this->model::getTable();
        $this->searchableProperties = $this->model::getPropertiesForSearch();

        $this->objectType = $this->extractKeyFromClass();
    }

    /**
     * This function will return Select query object with given column,filters,search,limit,offset and order by
     *
     * @param $columns
     * @param array $filters
     * @param string $searchValue
     * @param null $limit
     * @param null $offset
     * @param array $orderBy
     * @param bool $applyFilterModifier
     * @return Select
     */
    // protected function prepareQuery($columns, $filters = [], $searchValue = '', $limit = null, $offset = null, $orderBy = [], $applyFilterModifiers = true)
    // {
    //     $selectAll = new Select();
    //     $selectAll->columns($columns)->from($this->getTable());

    //     $searchParameters = $this->searchableProperties;
    //     $search = $this->getSearchFilter($searchValue, $searchParameters);
    //     if (!is_null($search)) {
    //         foreach ($search as $searchFilterWhere) {
    //             $selectAll->where($searchFilterWhere, Sql::ANY);
    //         }
    //     }

    //     $where = $this->getFiltersCondition($filters);

    //     if ($applyFilterModifiers) {
    //         $this->modifyFiltersCondition($filters, $where);
    //     }

    //     if (!is_null($where)) {
    //         $selectAll->where($where);
    //     }

    //     if ($limit !== null) {
    //         $selectAll->limit($limit);
    //     }

    //     if ($offset !== null) {
    //         $selectAll->offset($offset);
    //     }

    //     if (!empty($orderBy)) {
    //         $selectAll->orderBy($orderBy);
    //     }

    //     return $selectAll;
    // }

    /**
     * This method return a list of models according to the implementation of BaseRepository and BaseModel.
     * The whole database set of rows is returned and the filtering is not allowed.
     * If $applyFilterModifier is set to false, the modifyFiltersCondition function will not be called
     *
     * @param null $searchValue
     * @param bool $applyFilterModifier
     * @return array<BaseModel> || array
     * @throws ReflectionException
     */
    // public function findAll($searchValue = null)
    // {
    //     $orderByParams = $this->getOrderByParams();
    //     $rows = $this->dbSelect($this->prepareQuery('*', [], $searchValue, null, null, $orderByParams));
    //     return $this->convertToModelObjects($rows);
    // }

    public function findAll(){
        $rows = SessionUtil::retrieveDowntimeResults();
        return $this->convertToModelObjects($rows);
    }

    /**
     * This method return a list of models according to the implementation of BaseRepository and BaseModel.
     * The whole database set of rows is returned and the filtering is allowed.
     *
     * @param array $conditions
     * @param null $searchValue
     * @return array<BaseModel> || array
     * @throws ReflectionException
     */
    // public function findAllByFilters(array $conditions = [], $searchValue = null, $applyPrepareQueryFilterModifier = true) : array
    // {
    //     $result = [];
    //     $dbResult = $this->dbSelect($this->prepareQuery('*', $conditions, $searchValue, null, null, [], $applyPrepareQueryFilterModifier));
    //     if (!empty($dbResult)) {
    //         $result = $this->convertToModelObjects($dbResult);
    //     }

    //     return $result;
    // }

    /**
     * This method return a single model object according to the passed ID.
     *
     * @param $id
     * @return BaseModel || array
     * @throws ReflectionException
     */
    // public function findById($id)
    // {
    //     $result = NULL;

    //     $select = $this->prepareQuery('*',['id' => $id]);

    //     $dbResult = $this->dbSelect($select);
    //     if (!empty($dbResult)) {
    //         $result = $this->convertToSingleModelObject($dbResult[0]);
    //     }
    //     return $result;
    // }

    public function findById($id)
    {
        $result = NULL;

        $rows = SessionUtil::retrieveDowntimeResults();

        $row = array_filter(
            $rows,
            function($k) use ($id){
                return $k[0] == $id;
            }
        );

        if (!empty($row)) {
            $result = $this->convertToSingleModelObject($row[0]);
        }
        return $result;
    }

    /**
     * This method inserts the given model object into database
     *
     * @param BaseModel $model
     * @return mixed
     */
    // public function add(BaseModel $model)
    // {
    //     $values = $model->getValues();
    //     $result = $this->dbInsert($this->table, $values);
    //     if ($result == 0) {
    //         throw new \InvalidArgumentException('Failed to add new record in database');
    //     }
    //     return $result;
    // }

    /**
     * This method updates the db record with the values of the given model object
     *
     * @param BaseModel $model
     */
    // public function update(BaseModel $model)
    // {
    //     $modifiedValues = $model->getValues();
    //     $idColumn = $model->getColumnName('id');
    //     $id = $model->getId();

    //     $this->dbUpdate($this->table, $modifiedValues, [$idColumn . ' = ?' => $id]);
    // }

    /**
     * This method deletes the db record related to the given model object
     *
     * @param BaseModel $model
     */
    // public function delete(BaseModel $model)
    // {
    //     $idColumn = $model->getColumnName('id');
    //     $id = $model->getId();

    //     $this->dbDelete($this->table, [$idColumn . ' = ?' => $id]);
    // }

    /**
     * This method returns a database connection object
     */
    // protected function connectToDb()
    // {
    //     $configApp = Config::app('resources');
    //     $dbConf = null;
    //     if (isset($this->moduleName)) {
    //         $dbConf = $configApp->getSection($this->moduleName)->toArray();
    //     } elseif ($configApp->hasSection($this->getModuleName())) {
    //         $dbConf = $configApp->getSection($this->getModuleName())->toArray();
    //     }
    //     $this->dbConnection = new Connection($dbConf);
    // }

    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    protected function getModuleName()
    {
        if (empty($this->moduleName)) {
            $this->moduleName = $this->getIcingaRequest()->getModuleName();
        }
        return $this->moduleName;
    }

    // protected function dbInsert($table, $values)
    // {
    //     $this->dbConnection->insert($table, $values);
    //     return $this->getLastInsertId();
    // }

    // protected function dbUpdate($table, $modifiedValues, $where)
    // {
    //     $this->dbConnection->update($table, $modifiedValues, $where);
    // }

    // protected function dbDelete($table, $where)
    // {
    //     $this->dbConnection->delete($table, $where);
    // }

    // protected function dbSelect(Select $select)
    // {
    //     $results = [];
    //     $rows = $this->dbConnection->select($select)->fetchAll();
    //     foreach ($rows as $row) {
    //         $results[] = array_filter(
    //             $row,
    //             function ($key) {
    //                 return is_string($key);
    //             },
    //             ARRAY_FILTER_USE_KEY
    //         );
    //     }
    //     return $results;
    // }

    /**
     * It will create derived model class namespace and check if that exists or not
     * @return string
     * @throws ReflectionException
     */
    protected function getModelNamespace()
    {
        $className = $this->getModelClass();
        $this->checkIfModelExists($className);
        return $className;
    }

    /**
     * Check if class exists given as parameter
     * @throws Exception
     */
    protected function checkIfModelExists($className)
    {
        if (!class_exists($className)) {
            throw new Exception(sprintf('Trying to use invalid model %s', $className));
        }
    }

    /**
     * It will create derived model class namespace
     * @return string
     * @throws ReflectionException
     */
    protected function getModelClass()
    {
        $modelKeyName = $this->extractKeyFromClass();
        $className =  $modelKeyName . 'Model';
        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        $namespace = $this->removeRepositoryStr($namespace);

        return $namespace . 'Model\\' . $className;
    }

    /**
     * This function return class name after extract from repository name of derived class
     * @return false|string
     * @throws ReflectionException
     */
    protected function extractKeyFromClass()
    {
        $class = (new \ReflectionClass($this))->getShortName();
        return $this->removeRepositoryStr($class);
    }

    /**
     * Remove Repository string from classname
     * @param $str
     * @return false|string
     */
    protected function removeRepositoryStr($str)
    {
        $len = strlen('Repository');
        return substr($str, 0, - $len);
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model)
    {
        $this->model = $model;
    }

    /**
     * @return bool|string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @param bool|string $objectType
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;
    }

    protected function getModelObject()
    {
        return new $this->model();
    }

    /**
     * It will convert model array attributes to object
     * @param array $dataRows
     * @return array
     * @throws ReflectionException
     */
    public function convertToModelObjects (array $dataRows) {
        $results = [];

        foreach ($dataRows as $row) {
            $results[] = $this->convertToSingleModelObject($row);
        }

        return $results;
    }

    /**
     * It will create single object model with set constructor properties
     * @param array $data
     * @return object
     * @throws ReflectionException
     */
    public function convertToSingleModelObject (array $data) {
        $arguments = $this->prepareArgumentsForModelConstructor($data);
        $class = new \ReflectionClass($this->model);
        $modelObject = $class->newInstanceArgs($arguments);
        $this->setIfModelCanBeDeleted($modelObject);
        return $modelObject;
    }

    /**
     * It will set constructor for derived model class constructor with its attributes
     * @param array $row
     * @return array
     * @throws ReflectionException
     */
    protected function prepareArgumentsForModelConstructor (array $row) {
        // check model constructor arguments number
        $classMethod = new \ReflectionMethod($this->model, '__construct');
        $argumentsCount = count($classMethod->getParameters());

        // remove elements in $row which are greater than constructor arguments number
        if (count($row) > $argumentsCount) {
            array_splice($row, $argumentsCount);
        }
        elseif (count($row) < $argumentsCount) {
            throw new Exception(
                sprintf(
                'Error populating the model expecting more arguments for %s constructor (data: %s)',
                    $this->model,
                    serialize($row)
                )
            );
        }

        return $row;
    }

    /**
     * It will return last inserted id of record from database
     * @return mixed
     */
    // public function getLastInsertId()
    // {
    //     $id = (new Select())->columns('LAST_INSERT_ID() AS LAST_INSERT_ID');
    //     $this->dbConnection->select($id)->fetchAll();
    //     return $this->dbConnection->select($id)->fetchColumn(0);
    // }

    /**
     * Return the request associated with this form
     *
     * Returns the global request if none has been set for this form yet.
     *
     * @return \Icinga\Web\Request
     * @throws \Icinga\Exception\ProgrammingError
     */
    public function getIcingaRequest()
    {
        if ($this->icingaRequest === null) {
            $this->icingaRequest = Icinga::app()->getRequest();
        }

        return $this->icingaRequest;
    }

    /**
     * This function takes search value, params and return array of converted filters to db like query
     *
     * @param $searchValue
     * @param $searchParameters
     * @return array|null
     */
    // protected function getSearchFilter($searchValue, $searchParameters)
    // {
    //     $search = [];
    //     if (!empty($searchValue)) {
    //         foreach ($searchParameters as $key) {
    //             $search[] = [$key . ' like ?' => '%' . $searchValue . '%'];
    //         }
    //     }

    //     return $search;
    // }

    /**
     * This function takes filters conditions as array and return array of converted filters to db where query.
     *
     * In case the $value of a $key-$value pair is of type array, the query will filter the rows whose is
     * contained in the array $value
     *
     * @param array $filters
     * @return array|null
     */
    // protected function getFiltersCondition(array $filters)
    // {
    //     $where = null;
    //     if (!empty($filters)) {
    //         $where = [];
    //         foreach ($filters as $key => $value) {
    //             if (is_array($value)) {
    //                 $where[$key . ' IN (?)'] = $value;
    //             } else {
    //                 $where[$key . '=?'] = $value;
    //             }
    //         }
    //     }
    //     return $where;
    // }

    /**
     * This method is used to modify the filters passed to the query builder
     *
     * @param $filters
     * @param $where
     * @return void
     */
    protected function modifyFiltersCondition($filters, &$where)
    {
        //
    }

    /**
     * This function locks one or more DB tables
     *
     * @param array $tables Array containing table names
     * @param bool  $write Set to true if you need to lock the writing permissions.
     *                     By default is set to read.
     */
    // public function lockTables(array $tables, bool $write = false)
    // {
    //     $mode = ($write) ? 'WRITE' : 'READ';

    //     foreach ($tables as &$table) {
    //         $table = $table . ' ' . $mode;
    //     }
    //     $tables = implode(', ', $tables);
    //     $queryString = sprintf('LOCK TABLES %s', $tables);
    //     $this->execDbQuery($queryString);
    // }

    /**
     * This function unlocks all the DB tables
     */
    // public function unlockTables()
    // {
    //     $this->execDbQuery('UNLOCK TABLES;');
    // }

    /**
     * TThis function will execute the passed DB query.
     * Refactored to satisfy the unit test case
     * @param $query
     */
    // protected function execDbQuery($query)
    // {
    //     $this->dbConnection->exec($query);
    // }

    /**
     * Return distinct records using given column name as parameter
     *
     * @param $column
     * @return array|null
     */
    // protected function findDistinct($column) : ?array
    // {
    //     $columns = 'DISTINCT(' . $column . ')';
    //     $query = $this->prepareQuery($columns, [], '', null, null, [$column => SORT_DESC]);
    //     return $this->fetchAll($query);
    // }

    /**
     * Fetch and return all result as associative array
     *
     * @param $query
     * @return mixed
     */
    // protected function fetchAll($query)
    // {
    //     return $this->dbConnection->select($query)->fetchAll(PDO::FETCH_ASSOC);
    // }

    /**
     * Check if the model object can be deleted
     *
     * @param $modelObject
     * @return void
     */
    protected function setIfModelCanBeDeleted($modelObject)
    {
        $modelObject->setCanBeDeleted(true);
    }

    /**
     * This function return array of mysql order_by params, can be override in derived class
     * @param null $value
     * @param null $direction
     * @return array
     */
    // public function getOrderByParams($value = null, $direction = null)
    // {
    //     $orderByParam = [];
    //     if ($value !== null) {
    //         if ($direction === null) {
    //             $direction = SORT_ASC;
    //         }
    //        $orderByParam = [$value => $direction];
    //     }
    //     return $orderByParam;
    // }

    /**
     * This method will return the array of the objects data. It will be called by BaseApiHandle class (GET).
     *
     * @param null $id
     * @return array
     * @throws ReflectionException
     */
    // public function listObjectData($id = null)
    // {
    //     $objectsArray = [];
    //     if ($id) {
    //         $objectsData = $this->findById($id);
    //         if (!empty($objectsData)) {
    //             $objectsArray = $objectsData->toArray();
    //         }
    //     } else {
    //         $objects = $this->findAll();
    //         foreach ($objects as $objectModel) {
    //             $objectsArray[] = $objectModel->toArray();
    //         }
    //     }

    //     return $objectsArray;
    // }
}



function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
  }