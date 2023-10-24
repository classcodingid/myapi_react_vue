<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;

class BlogController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }

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

        //upload file ke do_spaces
        //========================
        //sumber file dari form
        $image = $request->file('image');
        //kompres image
        $img = Image::make($image);
        $img->fit(500, null, null, 'top');
        //file nama yang sudah di enkripsi hash
        $file = pathinfo($image->hashName(), PATHINFO_FILENAME);
        //dapatkan ektensi file
        $ext = $request->file('image')->getClientOriginalExtension();
        //namafile untuk store database
        $filestore = $file . '_' . time() . '.' . $ext;
        //path di storage
        $path = 'public/myapigateway/blog/';
        //upload image ke storage s3
        Storage::disk('do_spaces')->put($path . $filestore, $img->stream(), 'public');
        //========================

        //upload image
        // $image = $request->file('image');
        // $image->storeAs('public/blogs', $image->hashName());

        //create post
        $blog = Blog::create([
            'image'     => $filestore,
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
        $blog = Blog::findOrFail($id);

        //cek keberadaan image
        if ($request->hasFile('image')) {
            //upload file ke do_spaces
            //========================
            //sumber file dari form
            $image = $request->file('image');
            //kompres image
            $img = Image::make($image);
            $img->fit(500, null, null, 'top');
            //file nama yang sudah di enkripsi hash
            $file = pathinfo($image->hashName(), PATHINFO_FILENAME);
            //dapatkan ektensi file
            $ext = $request->file('image')->getClientOriginalExtension();
            //namafile untuk store database
            $filestore = $file . '_' . time() . '.' . $ext;
            //path di storage
            $path = 'public/myapigateway/blog/';
            //delete image lama
            //kuncinya di parse_url
            Storage::disk('do_spaces')->delete(parse_url($path . basename($blog->image)));
            //upload image baru ke storage s3
            Storage::disk('do_spaces')->put($path . $filestore, $img->stream(), 'public');
            //========================

            //update post with new image
            $blog->update([
                'image'     => $filestore,
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
        $blog = Blog::findOrFail($id);

        //========================
        //delete image
        $path = 'public/myapigateway/blog/';
        //kuncinya di parse_url
        Storage::disk('do_spaces')->delete(parse_url($path . basename($blog->image)));
        //========================

        //delete blog
        $blog->delete();

        //return response
        return new BlogResource(true, 'Data blog Berhasil Dihapus!', null);
    }
}
