<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * model to help fix laravel issue with composite keys
 *
 * @param string $table
 * @param array $search array( $key => $value )
 */
class ckModel
{

    /**
     * tables name
     *
     * @var string
     */
    private $tableName;

    /**
     * query search keys
     *
     * @var array
     */
    private $ids;

    /**
     * query search data
     *
     * @var array
     */
    private $search;

    /**
     * loaded data
     *
     * @var array
     */
    private $data;

    /**
     * has loaded table been changed
     *
     * @var boolean
     */
    private $hasChanged = FALSE;

    /**
     * model to help fix laravel issue with composite keys
     *
     * @param string $table
     * @param array $search array( $key => $value )
     */
    public function __construct($table, $search)
    {
        $this->tableName = $table;

        foreach ($search as $key => $value) {
            $this->ids[]    = $key;
            $this->search[]   = $value;
        }
        $this->searchDB();
    }

    /**
     * return data from related $key, if not found returns null
     *
     * @param string $key
     * @return void
     */
    public function __get($key)
    {
        return (in_array($key, array_keys($this->data))) ?
            $this->data[$key] :
            NULL;
    }

    /**
     * set value into stored data
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if (in_array($key, array_keys($this->data))) {
            $this->hasChanged = TRUE;
            return $this->data[$key] = $value;
        }
        return;
    }

    /**
     * generic save function
     *
     * @return boolean
     */
    public function save()
    {
        if ($this->hasChanged) {

            $where = $this->createWhere();
            $table = $this->tableName;

            foreach ($this->data as $key => $val) {
                $set[] = 't.' . $key . " = '" . $this->prepareValue($val) . "'";
            }

            $set =  implode(',', $set);

            $SQL = "UPDATE $table t
                SET $set
                WHERE $where";

            return DB::update($SQL);
        }
        return FALSE;
    }

    /**
     * remove item from database
     *
     * @return boolean
     */
    public function delete()
    {

        $where = $this->createWhere(FALSE);
        $table = $this->tableName;

        return DB::delete("DELETE FROM $table WHERE $where LIMIT 1");
    }

    /**
     * query db for table data
     */
    private function searchDB()
    {
        $where = $this->createWhere();
        $table = $this->tableName;

        $sql = "SELECT t.*
            FROM $table t
            WHERE $where
            LIMIT 1";

        foreach ((DB::select($sql))[0] as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    /**
     * formates and returns data into where statment
     *
     * @param boolean $useT prefix columns with 't.'
     * @return string
     */
    private function createWhere($useT = TRUE)
    {
        for ($i = 0; $i < count($this->ids); $i++) {
            $where[] = (($useT) ? 't.' : '') . $this->ids[$i] . '= "' . $this->search[$i] . '"';
        }
        return implode(' AND ', $where);
    }

    /**
     * post treatment on values to stop db search breaking
     *
     * @param string $val
     * @return string
     */
    private function prepareValue($val)
    {
        return str_replace('"', '\"', $val);
    }
}
