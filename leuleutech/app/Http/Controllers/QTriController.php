<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
use Carbon\Carbon;
class QTriController extends Controller
{
    public function Home()
    {
        return view('quantri.pages.home');
    }

    // POST
    //Show list post
    public function listPost()
    {
        $listPost = DB::table('posts')->get();
        return view('quantri.pages.post.postList', ['listPost' => $listPost]);
    }
    public function createPost()
    {
        $cates = DB::table('categories')->get();
        return view('quantri.pages.post.postCreate', ['cates' => $cates]);
    }
    public function Create(Request $request){

        $txttitle = $request->input('txttitle');
        $txtdesc = $request->input('txtdesc');
        $txtcontent = $request->input('txtcontent');
        $txtCateID = $request->input('txtCateID');
        $time=Carbon::now();

        //TODO: Lưu hình thẻ khi có file hình
        $gethinhanh = '';
         if($request->hasFile('hinhanh')){
            //Hàm kiểm tra dữ liệu
            $this->validate($request,
                [
                    //Kiểm tra đúng file đuôi .jpg,.jpeg,.png.gif và dung lượng không quá 2M
                    'hinhanh' => 'mimes:jpg,jpeg,png,gif|max:15360',
                ],
                [
                    //Tùy chỉnh hiển thị thông báo không thõa điều kiện
                    'hinhanh.mimes' => 'Chỉ chấp nhận hình thẻ với đuôi .jpg .jpeg .png .gif',
                    'hinhanh.max' => 'Hình thẻ giới hạn dung lượng không quá 15M',
                ]
            );

            //Lưu hình ảnh vào thư mục public/upload/hinhanh
            $hinhanh = $request->file('hinhanh');
            $gethinhanh = time().'_'.$hinhanh->getClientOriginalName();
            $destinationPath = public_path('/uploads/hinhanh');
            $hinhanh->move($destinationPath, $gethinhanh);
         }
        DB::insert('insert into posts (PostName, PostDescription, PostContent, PostPhoto, PostDate, PostView, CateId)
         values (?, ?, ?, ?, ?, ?, ?)', [$txttitle, $txtdesc, $txtcontent, $gethinhanh, $time, 0, $txtCateID]);

        return redirect('quantri/dsbaiviet')->with('success','Data Added');

    }

    //Category
    public function listCate()
    {
        $listCate = DB::table('categories')->get();

        return view('quantri.pages.category.cateList', ['listCate' => $listCate]);
    }

}
