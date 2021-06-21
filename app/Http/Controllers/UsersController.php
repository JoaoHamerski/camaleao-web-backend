<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Util\Sanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::latest()->paginate(10),
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validator(
            $data = $this->getFormattedData($request->all())
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return response()->json([
            'message' => 'success',
            'redirect' => route('users.index')
        ], 200);
    }

    public function patch(Request $request)
    {
        $user = auth()->user();

        $validator = $this->validator(
            $data = $this->getFormattedData($request->all()),
            true,
            $user
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => empty(trim($data['password'])) 
                ? $user->password
                : bcrypt($data['password'])        
            ]);

        return response()->json([
            'message' => 'success',
            'redirect' => route('users.my-account')
        ], 200);
    }

    public function destroy(User $user) 
    {
        $user->delete();

        return response()->json([
            'message' => 'success',
            'redirect' => route('users.index')
        ], 200);
    }

    public function destroyOwnAccount()
    {
        auth()->user()->delete();

        return response()->json([
            'message' => 'success',
            'redirect' => route('auth.showLoginForm')
        ], 200);
    }

    public function myAccount() 
    {
        return view('users.my-account', [
            'user' => auth()->user(),
            'roles' => Role::all()
        ]);
    }

    public function changeRole(Request $request, User $user) 
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error', 
                'errors' => $validator->errors()
            ], 422);
        }

        if ($user->id == \Auth::user()->id) {
            return response()->json([
                'message' => 'success',
                'redirect' => route('users.index')
            ]);
        }

        $user->role_id = $request->role_id;
        $user->save();

        return response()->json([
            'message' => 'success',
            'redirect' => route('users.index')
        ], 200);
    }

    public function getChangeRoleForm(User $user)
    {
        return response()->json([
            'message' => 'success',
            'view' => view('users._change-role-form', [
                'user' => $user,
                'roles' => Role::all()
            ])->render()
        ], 200);
    }

    public function getFormattedData($data) 
    {
        if (isset($data['name']) && ! empty($data['name'])) {
            $data['name'] = Sanitizer::name($data['name']);
        }

        return $data;
    }

    public function validator(array $data, $isUpdate = false, $user = null)
    {
        return Validator::make($data, [
            'name' => 'required|max:191',
            'email' => [
                'required', 'email', $isUpdate
                ? Rule::unique('users')->ignore($user->email, 'email')
                : Rule::unique('users') 
            ],
            'password' => $isUpdate ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6',
            'role_id' => 'sometimes|required|exists:roles,id'
        ]);
    }
}
