<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Requests\RequestCategory;
use App\Models\Category;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use vendor\project\StatusTest;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::select('id','c_name','c_title_seo','c_active','c_home')->get();

        $viewData=[
            'categories' => $categories
        ];

        return view('admin::category.index', $viewData);
    }

    public function create(){
        return view('admin::category.create');
    }
//thêm sản phẩm
    public function store(RequestCategory $requestCategory)
    {
//        dd($requestCategory->all());
       $this -> insertOrUpdate($requestCategory);
        return redirect()->back()->with('success','Thêm mới thành công');

    }
    public function edit($id){
        $category = Category::find($id);
        return view('admin::category.update',compact('category'));
    }
//sửa sản phẩm
    public function update(RequestCategory $requestCategory,$id)
    {
        $this -> insertOrUpdate($requestCategory,$id);
        return redirect()->back()->with('success','Cập nhật dữ liệu thành công');
    }

    public function insertOrUpdate($requestCategory,$id='')
    {
        $code = 1;
        try{
            $category = new Category();

            if($id){
                $category = Category::find($id);
            }
            $category->c_name = $requestCategory->name;
            $category->c_slug = str_slug($requestCategory->name);
            $category->c_icon = str_slug($requestCategory->icon);
            $category->c_title_seo = $requestCategory->c_title_seo ? $requestCategory->c_title_seo : $requestCategory->name;
            $category->c_description_seo = $requestCategory->c_description_seo;
            $category->save();
        }catch (\Exception $exception)
        {
            $code = 0;
            Log::error("[Error insertOrUpdate Categories ]".$exception->getMessage());
        }
        return $code;
    }

    public function action($action,$id)
    {
        $messages ='';
        if($action)
        {
            $category = Category::find($id);
            switch ($action)
            {
                case 'delete':
                    $category->delete();
                    $messages = 'Xóa dữ liệu thành công';
                    break;
                case 'active':
                    $category->c_active = $category->c_active == 1 ? 0 : 1;
                    $category->save();
                    break;
                case 'home':
                    $category->c_home = $category->c_home == 1 ? 0 : 1;
                    $category->save();
                    break;
            }
        }
        return redirect()->back()->with('success',$messages);
    }
}
