<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     *    @OA\Get(
     *       path="/blogs",
     *       tags={"Blog"},
     *       operationId="DataBlog",
     *       summary="Data Blog",
     *       description="Mengambil Data Blog",
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *               "success": true,
     *               "message": "Berhasil mengambil Data Blog",
     *               "data": {
     *                   {
     *                   "id": "1",
     *                   "title": "Title",
     *                  }
     *              }
     *          }),
     *      ),
     *  )
     */
    public function index()
    {
        //get all posts
        $blogs = Blog::latest()->paginate(5);

        //return collection of posts as a resource
        return new BlogResource(true, 'List Data Blogs', $blogs);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/blogs', $image->hashName());

        //create post
        $blog = Blog::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        //return response
        return new BlogResource(true, 'Data blog Berhasil Ditambahkan!', $blog);
    }

    public function show($id)
    {
        //find blog by ID
        $blog = Blog::find($id);

        //return single blog as a resource
        return new BlogResource(true, 'Detail Data blog!', $blog);
    }

    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $blog = Blog::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/blogs', $image->hashName());

            //delete old image
            Storage::delete('public/blogs/' . basename($blog->image));

            //update post with new image
            $blog->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        } else {

            //update post without image
            $blog->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        //return response
        return new BlogResource(true, 'Data blog Berhasil Diubah!', $blog);
    }

    public function destroy($id)
    {

        //find blog by ID
        $blog = Blog::find($id);

        //delete image
        Storage::delete('public/blogs/' . basename($blog->image));

        //delete blog
        $blog->delete();

        //return response
        return new BlogResource(true, 'Data blog Berhasil Dihapus!', null);
    }
}
