<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repo\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function userlist(Request $request)
    {
        try {
            $context = "user list";
            $parameter = $request->all();
            $data = $this->user->list($parameter);
            return $this->response($data, 200, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, "Something went wrong.");
        }
    }

    public function index(Request $request)
    {
        $context = "User List";
        try {
            $this->validate($request, [
                "filter_field" => "sometimes|string",
                "filter_value" => "required_with:filter_field|string",
                "q" => "sometimes",
            ]);
        } catch (\Exception $ex) {
            return $this->message($ex->response->original, 422, $context);
        }

        try {
            $parameter = $request->all();
            $parameter["sort_by"] = $request->get("sort_by", "desc");
            $parameter["sort_field"] = $request->get("sort_field");
            $parameter["limit"] = $this->limit($request);
            $path = $request->url();

            $data = $this->user->getAllWithParam($parameter, $path);
            if (count($data) == 0) {

                return $this->message("No record found", 404, $context);
            }
            return $this->response($data, 200, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, "Something went wrong.");
        }
    }

    public function store(Request $request)
    {
        $context = "User Create";
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "username" => "required|string|unique:users,username|min:4|max:32|regex:/^[a-z0-9_.]+$/",
            "email" => "required|email|unique:users,email",
            'password' => "required|min:8|max:15",
            "email_verified_at" => "sometimes",
            "remember_token" => "sometimes|string",
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $create = $request->only('name', 'username', 'email');
            $create['password'] = Hash::make($request->input('password'));
            $user = $this->user->create($create);
            return $this->message("User created successfully", 200, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, "Something went wrong.");
        }
    }

    public function show($id)
    {
        $context = "User by Id";
        try {
            $data = $this->user->getSpecificById($id);
            return $this->response($data, 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 404, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, "Something went wrong.");
        }
    }

    public function update($id, Request $request)
    {
        $context = "Update User";
        try {
            $user = $this->user->getSpecificById($id);

            $validator = Validator::make($request->all(), [
                "name" => "sometimes|string",
                "username" => "sometimes|string|unique:users,username,$user->id,id|min:4|max:32|regex:/^[a-z0-9_.]+$/",
                "email" => "sometimes|email|unique:users,email,$user->id,id",
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $create = $request->only('name', 'username', 'email');
            $user = $this->user->update($user->id, $create);
            return $this->message("User updated successfully", 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 404, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, "Something went wrong.");
        }
    }

    public function delete($id)
    {
        $context = "Delete User";
        try {
            $user = $this->user->getSpecificById($id);
            $this->user->delete($user->id);
            return $this->message("User deleted successfully", 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 404, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $context, "Something went wrong.");
        }
    }

    public function changePwd($id, Request $request)
    {
        $context = "Change User's Password";
        $user = $this->user->getSpecificById($id);

        $messages = [
            "password.required" => "The password field is required.",
            "conf_password.required" => "The conf password field is required.",
        ];

        $validator = Validator::make($request->all(), [
            'password' => "required|min:8|max:15",
            "conf_password" => "required|same:password"
        ], $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $update['password'] = Hash::make($request->input('password'));
            $this->user->update($user->id, $update);

            return $this->message('User Password Updated Successfully', 200, $context);
        } catch (ModelNotFoundException $ex) {
            return $this->message('No Record Found', 404, $context);
        } catch (QueryException $exception) {
            return $this->message($exception->getTraceAsString(), 521, $context, "Something went wrong.");
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500,  $context, "Something went wrong.");
        }
    }


}
