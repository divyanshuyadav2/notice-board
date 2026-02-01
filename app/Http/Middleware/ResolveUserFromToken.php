<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class ResolveUserFromToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
       // ON SERVER I WILL GET THE TOKEN FROM SESSION FOR LOCAL I AM DOING LIKE THIS
        // $request->merge([
        // 'token'=>'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3BhcnRha2VkaWdpdGFsIiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdC9wYXJ0YWtlZGlnaXRhbCIsImlhdCI6MTc2ODUwMjYxMSwiYWNjZXNzX3Rva2VuIjoiNjRmMTM3ZWQyNjFiZmVjZDFjNzQ5YjJmNWY5OWMyIiwiVXNlcl9VSU4iOiIxNjc3ODUxNTkyIn0.PRg2hdtwX4Zm_rI41wK4hXwSUMe2Byu-4GCydIJdHGQ'
        // ]);
        // if(!$request->has('token')){
        //     return response()->json([
        //         'message'=>'Unauthenticated'
        //     ],401);
        // }
        $token=session('token');
        if(app()->environment('local') && ! $token){
          $token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3BhcnRha2VkaWdpdGFsIiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdC9wYXJ0YWtlZGlnaXRhbCIsImlhdCI6MTc2OTcwNzA1MSwiYWNjZXNzX3Rva2VuIjoiNmRhMTBkMjgwOWNlM2Q1ZjY3NjU4ZmRiOWUzNjg2IiwiVXNlcl9VSU4iOiIxNzY2MDYxNTcyIn0.v0uGL0nAZ_wTPL-qckHdJ_FhQNgzQJBDbZKpnd3pzlY';}
        if(!$token){
            return response()->view('errors.sessionexpires',[],401);
        }
        $response = Http::timeout(10)->post(
         config('services.partakers.user_api'),
         [
            'token'=>$token
         ]
        );
     //   dd($response);
        if(!$response->ok()){
            return response()->view('errors.sessionexpires',[],401);
        }
        $data=$response->json();

        session([
            'token' => $token,
            'User_UIN'  => $data['data'][0]['User_UIN'],
            'user_data' => $data['data'][0],
        ]);

        return $next($request);
    }
    }
