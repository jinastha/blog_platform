<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repo\Interfaces\CategoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected $category;
    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }
    
    public function index(Request $request)
    {
        $context = "Category List";
        try {
            $sortBy = $request->get("sort_by", "desc");
            $sortField = $request->get("sort_field");
            try {
                $this->validate($request, [
                    "filter_field" => "sometimes|string",
                    "filter_value" => "required_with:filter_field|string",
                    "q" => "sometimes",
                ]);
            } catch (\Exception $ex) {
                return $this->message($ex->response->original, 422, $context);
            }

            $limit = $this->limit($request);

            $parameter = $request->all();
            $parameter["sort_by"] = $sortBy;
            $parameter["sort_field"] = $sortField;
            $parameter["limit"] = $limit;
            $path = $request->url();

            $data = $this->category->getAllWithParam($parameter, $path);

            if (count($data) == 0) {
                return $this->message('No record found', 404, $context);
            }
            return $this->response($data, 200, $context);

        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context);
        } catch (\Error $ex) {
            return $this->message($ex->getMessage(), 500, $context, 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        $context = "Add category";

        $validator = Validator::make($request->all(), [
            "title" => "required",
            "content"=> "required",
            "user_id"=> "required",
        ]);
        if ($validator->fails()) { 
            return response()->json($validator->errors(), 422);
        }
        try {
            $create = $request->all();
            $this->category->create($create);
            return $this->message('Category Added Successfully', 200, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, 'Something went wrong');
        }
    }

    public function show($id)
    {
        $context = "Show category";
        try {
            $data = $this->category->getSpecificById($id);
            return $this->response($data, 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message('No record found', 404, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $context = "Update category";
        try {
            $category = $this->category->getSpecificByIdOrSlug($id);

            $validator = Validator::make($request->all(), [
                "title" => "required",
                "content"=> "required",
                "user_id"=> "required",
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $create = $request->only('title', 'content', 'user_id');
            $this->category->update($category->id, $create);
            return $this->message('Category Updated Successfully', 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message('No record found', 404,$context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, $ex->getMessage());
        }
    }

    public function delete($id)
    {
        $context = "Delete Category";
        try {
            $category = $this->category->getSpecificByIdOrSlug($id);

            $this->category->delete($category->id);
            return $this->message('Category Deleted Successfully', 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message('No record found', 404, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, "Something went wrong.");
        }
    }

    public function list(Request $request)
    {
        try {
            $context = "List of category";
            $parameter = $request->all();
            $data = $this->category->list($parameter);
            return $this->response($data, 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message($ex->getMessage(), 404, $context, 'Resource not found.');
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, "Something went wrong.");
        }
    }
}
