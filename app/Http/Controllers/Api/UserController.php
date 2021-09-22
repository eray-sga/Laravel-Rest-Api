<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $offset = $request->has('offset') ? $request->query('offset') : 0;
        $limit = $request->has('limit') ? $request->query('limit') : 10;

        $list = User::query();
        if($request->has('q')) //filtreleme, arama işlemleri yapar q parametresi üzerinden.
            $list->where('name','like','%' . $request->query('q') . '%');

        if($request->has('sortBy'))
            $list->orderBy($request->query('sortBy'), $request->query('sort','DESC'));
            //sortBy parametresi gönderirsek belirttiğimiz sütuna göre descending sıralar.

        return response($list->offset($offset)->limit($limit)->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->name;
        $user->password = bcrypt($request->password);

        $user->save();

        return response([
            'data' => $user,
            'message' => "user created"
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        

        $user->save();

        return response([
            'data' => $user,
            'message' => "user updated"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response([
            "message" => "user deleted"
        ], 200);
    }

    public function custom1(){
        //$user2 = User::find(2);
        //return new UserResource($user2); belirli bir kaydı döndük

        $users = User::all();
        //return UserResource::collection($users); tüm kaydı döndük
        //return new UserCollection($users); Collection ile ek sütunlarımızı kullanabiliriz
        return UserResource::collection($users)->additional([
            'meta' => [
                'total_users' => $users->count(),
                'custom' => 'value'
            ]
        ]);
    }
}
