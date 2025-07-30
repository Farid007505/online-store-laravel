<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Page;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class pageController implements HasMiddleware
{
    public static function middleware(): array
        {
           return [
            (new Middleware('permission:pages view'))->only(['index']),
            (new Middleware('permission:pages edit'))->only(['edit']),
            (new Middleware('permission:pages create'))->only(['create']),
            (new Middleware('permission:pages destroy'))->only(['destroy']),
        ];
        }
    public function index(Request $request){
        $pages=Page::latest();
        if ($request->keyword !='') {
            $pages=$pages->where('name','like','%'.$request->keyword.'%');
        }
        $pages=$pages->paginate(10);
        $data['pages']=$pages;
        return view('admin.pages.list',$data);
    }

    public function create(){
        return view('admin.pages.create');
    }

    public function store(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required',
        ]);
        if ($validator->passes()) {
            $page=New Page;
            $page->name=$request->name;
            $page->slug=$request->slug;
            $page->content=$request->content;
            $page->save();

            $message='Page Create Successfully';
            session()->flash('success',$message);
            return response()->json([
                'status'=>true,
                'message'=>$message
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
            ]);
        }
    }

    public function edit(Request $request,$id){
        $page=Page::find($id);
        if ($page==Null) {
            $message='Record Not Found';
            session()->flash('error',$message);
            return redirect()->route('pages.index');
        }
        $data['page']=$page;
        return view('admin.pages.edit',$data);
    }


   public function update(Request $request,$id){

        $page=Page::find($id);
        if ($page==Null) {
            $message='Record Not Found';
            session()->flash('error',$message);
            return redirect()->route('pages.index');
        }
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required',
        ]);
        if ($validator->passes()) {
            $page->name=$request->name;
            $page->slug=$request->slug;
            $page->content=$request->content;
            $page->save();

            $message='Page Update Successfully';
            session()->flash('success',$message);
            return response()->json([
                'status'=>true,
                'message'=>$message
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
            ]);
        }
    }

     public function destroy(Request $request,$id){
        $page=Page::find($id);
        if ($page==Null) {
            $message='Record Not Found';
            session()->flash('error',$message);
            return reponse()->json([
                'status'=>true,
                'message'=>$message
            ]);

        }
        $page->delete();
            $message='Page Delete Successfully';
            session()->flash('success',$message);
            return response()->json([
                'status'=>true,
                'message'=>$message
            ]);
    }
}