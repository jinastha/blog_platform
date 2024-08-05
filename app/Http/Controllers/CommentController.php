<?php

namespace App\Http\Controllers;

use App\Repo\Interfaces\CommentInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\UnauthorizedException;

class CommentController extends Controller
{
    protected $comment;
    public function __construct(CommentInterface $comment)
    {
        $this->comment = $comment;
    }
    public function store($post_id, Request $request)
    {
        $context = "post comments";
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
            'user_id'   => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $create = $request->all();
            $create['post_id'] = $post_id;

            $created = $this->comment->create($create);
            $response = [
                "msg" => "Comment Successfully posted.",
                "data" => $created
            ];

            return $this->response($response, 200, $context);
        } catch (\Exception $ex) {  dd($ex);
            return $this->message($ex->getMessage(), 500, $context, $ex->getMessage());
        }
    }

    public function update($id, $post_id, Request $request)
    {
        $context = "update comments";
        $data = $this->comment->getById($id);
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
            'user_id'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $create = $request->all();
            $create['post_id'] = $post_id;
            $this->comment->update($id, $create);
            return $this->message('Comment Successfully updated.', 200, $context);
        } catch (UnauthorizedException $ex) {
            return $this->message($ex->getMessage(), 401, $context, $ex->getMessage());
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, $ex->getMessage());
        }
    }

    public function delete($id)
    {
        $context = 'Delete Comment';
        try {
            $data = $this->comment->getById($id);
            if (!$data) {
                return $this->message('Comment not found', 404, $context, 'Comment Not found');
            }
            $this->comment->delete($data->id);

            return $this->message('Comment Successfully deleted.', 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message($ex->getMessage(), 404, $context, 'Resource Not found');
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, 'Something went wrong');
        }
    }
}


