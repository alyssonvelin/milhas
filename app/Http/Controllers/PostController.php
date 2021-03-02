<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdatePost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        try
        {
            //$posts = Post::orderBy('title','desc')->paginate();
            $posts = Post::latest()->paginate();
            $filters = ['search'=>''];
            
            return view('posts.index',compact('posts','filters'));
        }
        catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
    }

    public function create()
    {
        try
        {
            return view('posts.create');
        }
        catch(Exception $e)
        {
            $e->getMessage();exit;
        }
    }

    public function store(StoreUpdatePost $request)
    {
        try
        {
            $data = $request->all();
            if($request->image->isValid())
            {
                //dd($request->image->extension());
                $nameFile = Str::of($request->title)->slug('-').'.'.$request->image->getClientOriginalExtension();
                //$image = $request->image->store('posts');
                $image = $request->image->storeAs('posts',$nameFile);
                $data['image'] = $image;
            }
                
            Post::create($data);
            return redirect()->route('posts.index')->with('message','Post cadastrado com sucesso');
        }
        catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
    }


    public function show($id)
    {
        try
        {
            //$post = Post::where('id',$id)->first();
            if(!$post = Post::find($id))
            {
                return redirect()->route('posts.index');
            }
            return view('posts.show',compact('post'));
        }
        catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
    }


    public function destroy($id)
    {
        try
        {
            if(!$post = Post::find($id))
            {
                return redirect()->route('posts.index');
            }
            
            if(Storage::exists($post->image))
                Storage::delete($post->image);

            $post->delete();
            return redirect()->route('posts.index')->with('message','Post removido com sucesso');
        }
        catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
    }


    public function edit($id)
    {
        try
        {
            //$post = Post::where('id',$id)->first();
            if(!$post = Post::find($id))
            {
                return redirect()->back();
            }
            return view('posts.edit',compact('post'));
        }
        catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
    }


    public function update(StoreUpdatePost $request,$id)
    {
        try
        {
            if(!$post = Post::find($id))
            {
                return redirect()->back();
            }

            $data = $request->all();
            if($request->image && $request->image->isValid())
            {
                if(Storage::exists($post->image))
                    Storage::delete($post->image);
                
                //dd($request->image->extension());
                $nameFile = Str::of($request->title)->slug('-').'.'.$request->image->getClientOriginalExtension();
                //$image = $request->image->store('posts');
                $image = $request->image->storeAs('posts',$nameFile);
                $data['image'] = $image;
            }

            $post->update($data);
            return redirect()->route('posts.index')->with('message','Post editado com sucesso');
        }
        catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
    }

    public function search(Request $request)
    {
        try
        {
            $posts = Post::where('title','LIKE',"%{$request->search}%")
                         ->orWhere('content','LIKE',"%{$request->search}%")
                         ->paginate(1);
            //$filters = $request->all();
            $filters = $request->except('_token');
            return view('posts.index',compact('posts','filters'));
        }
        catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
    }
}
