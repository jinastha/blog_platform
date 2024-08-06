<?php

namespace App\Http\Controllers;

use App\Repo\Interfaces\TagInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    protected $tag;
    public function __construct(TagInterface $tag)
    {
        $this->tag = $tag;
    }
    
    public function index(Request $request)
    {
        $context = "Tag List";
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

            $data = $this->tag->getAllWithParam($parameter, $path);

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
        $context = "Add tag";

        $validator = Validator::make($request->all(), [
            "key" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $create = $request->all();
            $this->tag->create($create);
            return $this->message('Tag Added Successfully', 200, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, 'Something went wrong');
        }
    }

    public function show($id)
    {
        $context = "Show tag";
        try {
            $data = $this->tag->getSpecificById($id);
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
        $context = "Update tag";
        try {
            $tag = $this->tag->getSpecificByIdOrSlug($id);

            $validator = Validator::make($request->all(), [
                "key" => "required"
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $create = $request->all();
            $this->tag->update($tag->id, $create);
            return $this->message('Tag Updated Successfully', 200, $context);
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
        $context = "Delete Tag";
        try {
            $tag = $this->tag->getSpecificByIdOrSlug($id);

            $this->tag->delete($tag->id);
            return $this->message('Tag Deleted Successfully', 200, $context);
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
            $context = "List of Tag";
            $parameter = $request->all();
            $data = $this->tag->list($parameter);
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

