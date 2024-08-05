<?php

namespace App\Repo\Eloquent;

use App\Repo\Interfaces\BaseInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseRepo implements BaseInterface
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get paginated data according to the given conditions passed.
     * @param $status
     * @param $sortBy
     * @param $limit
     * @return mixed
     */
    public function getAll($sortBy, $limit)
    {
        return $this->model->orderBy('id', $sortBy)->paginate($limit);
    }

    /**
     * Insert new row in related table.
     * @param array $data
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Insert multiple row in related table.
     * @param array $data
     */
    public function insert(array $data)
    {
        return $this->model->insert($data);
    }


    /**
     * Update row of given id in related table.
     * @param array $data
     * @param $id
     */
    public function update($id, array $data)
    {

        $this->model->findOrFail($id)->update($data);
        $data = $this->model->findOrFail($id);
        return $data;
    }

    /**
     * Delete row of given id in related table.
     * @param $id
     */
    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    /**
     * Get data related to given id in related table.
     * @param $id
     */
    public function getSpecificById($id)
    {
        $data = $this->model->findOrFail($id);
        return $data;
    }

    public function getAllWithParam(array $parameter, $path)
    {
        $columnsList = Schema::getColumnListing($this->model->getTable());

        //$is_columnExistInUserTable = false;
        $orderByColumn = "id";

        foreach ($columnsList as $columnName) {
            if ($columnName == $parameter["sort_field"]) {
                $orderByColumn = $columnName;
                break;
            }
        }
        $parameter["sort_field"] = $orderByColumn;
        if (isset($parameter["filter_field"])) {
            if (in_array($parameter["filter_field"], $columnsList)) {
                $data = $this->model->where($parameter["filter_field"], $parameter["filter_value"]);
            } else {
                $data = $this->model;
            }
        } else {
            $data = $this->model;
        }
        /**
         * Multiple filter Implementation
         */

        if (isset($parameter["filter"])) {
            $filterParams = $parameter["filter"];
            foreach ($filterParams as $key => $val) {
                /**
                 * Check if filter is needed from relationship or column of a table.
                 * If item count of $checkKey is 1 after exploding $key, filter from table column. Else use relation existence method for filter.
                 */
                $checkKey = explode(".", $key);
                $count = count($checkKey);
                if ($count == 1) {
                    $data = $data->where($key, "like", "$val%");
                } else {

                    $relationKey =  camel_case(implode(".", array_except($checkKey, [$count - 1])));

                    $data = $data->whereHas($relationKey, function ($query) use ($checkKey, $val) {
                        $query->where(last($checkKey), 'like', "$val%");
                    });
                }
            }
        }



        if (isset($parameter["start_date"])) {
            $data = $data->where('created_at', '>=', $parameter['start_date'] . ' 00:00:00');
        }
        if (isset($parameter["end_date"])) {
            $data = $data->where('created_at', '<=', $parameter['end_date'] . ' 23:59:59');
        }
        if (isset($parameter["with"])) {
            $data = $data->with($parameter["with"]);
        }

        return $data->orderBy($orderByColumn, $parameter["sort_by"])->paginate($parameter["limit"])->withPath($path)->appends($parameter);
    }

    public function getSpecificByColumnValue($column, $value)
    {
        return $this->model->where($column, $value)->first();
    }

    public function deleteMultipleByColumnValue($column, array $values)
    {
        return $this->model->whereIn($column, $values)->delete();
    }

    public function findByField($field, $value)
    {
        return $this->model->where($field, $value)->firstOrFail();
    }

    public function getSpecificByIdOrSlug($id)
    {
        $field = is_numeric($id) ? "id" : "slug";
        return $this->model->where($field, $id)->firstOrFail();
    }

    public function getAllByColumnValue($column, $value)
    {
        return $this->model->where($column, $value)->get();
    }


    public function getAllIn($column, $arrayValue)
    {
        return $this->model->whereIn($column, $arrayValue)->get();
    }

    public function createOrUpdate($matchThese, $data)
    {
        return $this->model->updateOrCreate($matchThese, $data);
    }

    public function list($params)
    {
        $data = $this->model;

        if (isset($params['filter']['name'])) {

            $data = $data->where('name', 'like', $params['filter']['name'] . '%');
        }
        return $data->get();
    }

    public function getAllList($params)
    {
        $data = $this->model;
        if (isset($params['filter'])) {
            foreach ($params['filter'] as $key => $value) {
                $data = $this->model->where($key, 'like', $value . '%');
            }
        }
        return $data->get();
    }

    public function getBySlug($slug)
    {
        return  $this->model->where('slug', $slug)->firstOrFail();
    }

}
