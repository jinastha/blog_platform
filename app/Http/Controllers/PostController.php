<?php

namespace App\Http\Controllers;

use App\Repo\Interfaces\PostInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $post;
    public function __construct(PostInterface $post)
    {
        $this->post = $post;
    }

    public function index(Request $request)
    {
        $context = "Post List";
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

            $data = $this->post->getAllWithParam($parameter, $path);

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
            "name" => "required",
            "user_id"=> "required",
            "tag"=> "required",
            "category"=> "required",
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $create = $request->all();
            $post = $this->post->create($create);
            $post->tags()->sync($request->input('tag'));
            $post->categories()->sync($request->input('category'));
            return $this->message('Post Added Successfully', 200, $context);
        } catch (QueryException $exception) { dd($exception);
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) { dd($ex);
            return $this->message($ex->getMessage(), 500, $context, 'Something went wrong');
        }
    }
    public function show($id)
    {
        $context = "Show post";
        try {
            $data = $this->post->getSpecificById($id);
            $data['tags'] = $data->tags;
            $data['categories'] = $data->categories;
            $data['comments'] = $data->comments;
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
        $context = "Update post";
        $post = $this->post->getSpecificById($id);

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "user_id"=> "required",
            "tag"=> "required",
            "category"=> "required",
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $create = $request->all();
            $post = $this->post->update($id,$create);
            $post->tags()->sync($request->input('tag'));
            $post->categories()->sync($request->input('category'));
            return $this->message('Post Updated Successfully', 200, $context);
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
        $context = "Delete post";
        try {
            $post = $this->post->getSpecificByIdOrSlug($id);

            $this->post->delete($post->id);
            return $this->message('Post Deleted Successfully', 200, $context);
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
            $context = "List of post";
            $parameter = $request->all();
            $data = $this->post->list($parameter);
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
