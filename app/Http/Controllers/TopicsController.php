<?php

namespace App\Http\Controllers;

use App\Exports\TopicsExport;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Auth;
use App\Handlers\ImageUploadHandler;
use App\Models\User;
use App\Models\Link;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request, Topic $topic, User $user, Link $link)
    {
        clock()->event('Importing tweets')->color('purple')->begin();
        $topics = $topic->withOrder($request->order)->paginate(20);
        $active_users = $user->getActiveUsers();
        $links = $link->getAllCached();
        clock()->event('Importing tweets')->end();

        return view('topics.index', compact('topics', 'active_users', 'links'));
    }

    public function show(Request $request, Topic $topic)
    {
        // URL 矫正
        if (!empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }

        $topic->visits()->increment();

        return view('topics.show', compact('topic'));
    }

    public function create(Topic $topic)
    {
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();

        return redirect()->to($topic->link())->with('success', '成功创建主题！');
    }

	public function edit(Topic $topic)
    {
        $this->authorize('update', $topic);
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('success', '更新成功！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '成功删除！');
	}

    public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }

    public function excel()
    {
        return view('topics.excel');
    }

    // public function export(Request $request, Topic $topic, User $user)
    // {
    //     $days = $request->days;
    //     // 查询 days 天之内的数据
    //     $topics = $topic->whereDate('created_at', '>=', now()->subDays($days))
    //                     ->with('category')
    //                     ->get();
    //
    //     // days 天内发帖的用户
    //     $users = $user->whereHas('topics', function($query) use ($days) {
    //         $query->whereDate('created_at', '>=', now()->subDays($days));
    //     })->get();
    //
    //     // excel 文件名
    //     $name = 'LaraBBS-topics-within-'.$days.'-days';
    //
    //     // 创建 excel
    //     \Excel::create($name, function($excel) use ($topics, $users) {
    //         // 话题数据表
    //         $excel->sheet('topics', function($sheet) use ($topics) {
    //             $sheet->appendRow(['id', '标题', '链接', '用户id', '分类名称','分类id', '阅读次数', '创建时间']);
    //             $rows = [];
    //
    //             foreach($topics as $topic) {
    //                 $rows[] = [
    //                     $topic->id,
    //                     $topic->title,
    //                     route('topics.show', $topic),
    //                     $topic->user_id,
    //                     $topic->category->name,
    //                     $topic->category->id,
    //                     $topic->view_count,
    //                     $topic->created_at,
    //                 ];
    //             }
    //             $sheet->rows($rows);
    //         });
    //
    //         // 用户数据表
    //         $excel->sheet('users', function($sheet) use ($users) {
    //
    //             $sheet->appendRow(['id', '姓名', '手机', '邮箱', '是否绑定微信', '注册时间']);
    //             $rows = [];
    //
    //             foreach($users as $user) {
    //                 $rows[] = [
    //                     $user->id,
    //                     $user->name,
    //                     $user->phone,
    //                     $user->email,
    //                     $user->weixin_unionid || $user->weixin_openid,
    //                     $user->created_at,
    //                 ];
    //             }
    //             $sheet->rows($rows);
    //         });
    //     })->export('xls');
    // }
    public function export(Request $request, TopicsExport $topicsExport)
    {
        return $topicsExport->withinDays($request->days);
    }

    public function import(Request $request)
    {
        \Excel::load($request->excel, function($reader) {
            // 获取第一个 数据表
            $sheet = $reader;
            // 遍历数据表中的数据
            $sheet->each(function($topicData) {
                $topic = Topic::find((int)$topicData['id']);
                if (!$topic) {
                    return;
                }

                $topic->title = $topicData['标题'];
                $topic->category_id = (int)$topicData['分类id'];
                $topic->save();
            });
        });

        return redirect()->route('topics.excel')->with('success', '导入成功！');
    }
}
