<?php

namespace App\Repo\Interfaces;

interface  BaseInterface
{
    public function getAll($sortBy, $limit);

    public function create(array $data);

    public function insert(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function getSpecificById($id);

    public function getAllWithParam(array $parameter, $path);

    public function getSpecificByColumnValue($column, $value);

    public function deleteMultipleByColumnValue($column, array $values);

    public function getSpecificByIdOrSlug($id);

    public function getAllByColumnValue($column, $value);

    public function getAllIn($column, $arrayValue);

    public function createOrUpdate($matchThese, $data);

    public function list($params);

    public function getAllList($params);

    public function getBySlug($slug);
}
