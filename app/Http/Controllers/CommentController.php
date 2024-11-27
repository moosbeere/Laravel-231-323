<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'name'=>'required|min:4',
            'desc'=>'required|max:256'
        ]);
        $comment = new Comment;
        $comment->name=$request->name;
        $comment->desc=$request->desc;
        $comment->article_id=request('article_id');
        $comment->user_id = Auth::id();
        if ($comment->save()) return redirect()->route('article.show', $comment->article_id)
                                            ->with('status', 'Add comment success');
        return redirect()->route('article.show', $comment->article_id)
        ->with('status', 'Add comment failed');
    }

    public function edit($id){
        $comment = Comment::findOrFail($id);
        Gate::authorize('update_comment', $comment);
        return view('comment.update',['comment'=>$comment]);
    }

    public function update(Request $request, Comment $comment){
        Gate::authorize('update_comment', $comment);
        $request->validate([
            'name'=>'required|min:4',
            'desc'=>'required|max:256'
        ]);
        $comment->name = $request->name;
        $comment->desc = $request->desc;
        if($comment->save()) return redirect()->route('article.show',$comment->article_id)->with('status', 'Comment update success');
        return redirect()->back()->with('status', 'Comment update failed');
    }

    public function destroy(Comment $comment){
        Gate::authorize('update_comment', $comment);
        $comment->delete();
        return redirect()->back()->with('status', 'Comment delete success');
    }
}
