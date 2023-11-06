<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Comment;
use App\Models\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Dto\CommentDto;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

use App\Rules\FileSizeRule;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /* #region index*/

    public function index()
    {
        $raw_comments = Comment::with(['user:id,name', 'files'])
            ->select('comments.id', 'comments.content', 'comments.parent_id', 'comments.user_id', 'comments.commented_comment_id', 'comments.level', 'comments.created_at', 'ref.content as commented_comment_content')
            ->leftJoin('comments as ref', 'comments.commented_comment_id', '=', 'ref.id')
            ->orderBy('comments.created_at', 'desc')
            ->get();


        // dd($raw_comments);
        $tree_grouped_comments = $this->groupComments($raw_comments, 0);

        $paginated_comments = $this->paginateComments($tree_grouped_comments, 3);
        return Inertia::render('Home', [
            // 'comments' => $tree_grouped_comments,
            'comments' => $paginated_comments,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }
    /* #endregion */

    /* #region show*/
    public function show(Request $request)
    {
        $raw_comments = Comment::with('user:id,name,email')
            ->select('comments.id as comment_id', 'comments.content', 'comments.user_id', 'comments.created_at')
            ->where('comments.level', 0)
            ->get();

        // $comments = $raw_comments->map(function ($comment) {
        //     $comment->content = Str::limit($comment->content, 30);
        //     return $comment;
        // });

        $comments = $raw_comments->map(
            fn ($comment) =>
            [
                'content' => Str::limit($comment->content, 30),
                'comment_id' => $comment->comment_id,
                'email' => $comment->user->email,
                'created_at' => $comment->created_at,
                'name' => $comment->user->name,
                // 'user_id' => $comment->user->id,
            ]
        );
        // dd($comments);
        // "name", "email", "content", "created_at", 'comment_id'


        return Inertia::render('Add', [
            'data' => $request->all(),
            "comments" => $comments
        ]);
    }

    /* #endregion */

    /* #region  add*/

    private const Comment_VALIDATOR = [
        'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9]+$/',
        'email' => 'required|string|email|max:255|unique:' . User::class,
        'content' => 'required|string',

    ];

    private const Comment_ERROR_MESSAGES = [


        'max' => 'The field cannot exceed :max characters.',
        // 'email' => 'Please enter a valid email address.',
        'string' => 'Only letters and numbers are allowed in this field.',

    ];


    public function add(Request $request)
    {

        /* #region Validation*/
        $validated = $request->validate(
            self::Comment_VALIDATOR,
            self::Comment_ERROR_MESSAGES
        );

        if ($request->hasFile('files')) {

            $rules = [
                'files' => ['array', 'max:3', new FileSizeRule],
                'files.*' => 'file',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {

                return redirect()->back()->withErrors($validator);
            }
        }

        /* #endregion */


        // dd($request);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::password()),
        ]);


        $newComment = $user->comments()->create([
            "content" => $validated['content'],
            "parent_id" => $request->parent_id,
            "level" => $request->level,
            'commented_comment_id' => $request->commented_comment_id,
            'url' => $request->url,

        ]);

        $this->savingFiles($request, $newComment);


        return to_route('index');
        // return Inertia::render('Add', ['data' => $request->all()]);
    }
    /* #endregion */

    /* #region savingFiles  */
    public function savingFiles(Request $request, $newComment)
    {
        // Handle file uploads
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                // Check the file type
                $fileType = $file->getMimeType();
                $path = 'public/';

                if (str_starts_with($fileType, 'image/')) {

                    $path .= 'images/';
                    // $file = $file->resize();
                } elseif (str_starts_with($fileType, 'text/')) {

                    $path .= 'text/';
                } else {
                    $path .= 'other/';
                }
                $unique_name = uniqid() . '_' . $file->getClientOriginalName();
                $path .= $unique_name;
                $file->storeAs($path);


                $fileRecord = new File([
                    'original_name' => $file->getClientOriginalName(),
                    'unique_name' => $unique_name,
                    'path' => $path,
                    'file_type' => $fileType,
                ]);

                $newComment->files()->save($fileRecord);
            }
        };
    }
    /* #endregion   */

    /* #region  groupComments*/


    protected function groupComments($comments, int $level)
    {
        $children = [];
        $parents = [];
        $items = [];

        foreach ($comments as $comment) {
            if ($comment->level == $level + 2) {
                $children = $this->groupComments($comments, $level + 1);
            } elseif ($comment->level == $level + 1) {
                $children[] = new CommentDto($comment);
            } elseif ($comment->level == $level) {
                $parents[] = new CommentDto($comment);
            }
        }

        foreach ($parents as $parent_com) {
            $el = array_filter($children, function ($com) use ($parent_com) {
                return $com->parent_id == $parent_com->id;
            });

            foreach ($el as $com) {
                $parent_com->add($com);
            }

            $items[] = $parent_com;
        }

        return $items;
    }
    /* #endregion*/

    /* #region paginateComments */
    protected function paginateComments($comments, $perPage)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $commentsCollection = collect($comments);


        $currentPageItems = $commentsCollection->slice(($currentPage - 1) * $perPage, $perPage);


        $comments = new LengthAwarePaginator(
            $currentPageItems,
            $commentsCollection->count(),
            $perPage,
            $currentPage
        );
        return $comments;
    }
    /* #endregion */
}
