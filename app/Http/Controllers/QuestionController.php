<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $questions = Question::with('votes', 'user')->withCount('answers');

        $sortedBy = $request->query(config('constants.sorted_by'), config('constants.newest'));
        switch ($sortedBy) {
            case config('constants.active'):
                $questions = $questions->orderByDesc('activities_count');
                break;
            case config('constants.unanswered'):
                $questions = $questions->withCount('answers')
                    ->orderBy('answers_count')
                    ->orderBy('created_at');
                break;
            default:
                $questions = $questions->orderByDesc('created_at');
        }

        $questions = $questions->paginate(config('constants.questions_per_page'));
        $questions->map(function ($answer) {
            $answer->sum_votes = $answer->votes->sum('vote');
        });

        $numberOfQuestions = Question::count();

        return view('post.list', compact(
            'questions',
            'numberOfQuestions',
            'sortedBy'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Question::class);

        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuestionRequest $request)
    {
        $this->authorize('create', Question::class);

        $question = Question::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'views_number' => config('constants.initial_view_number'),
            'activities_count' => config('constants.zero'),
        ]);

        $question->contents()
            ->create([
                'content' => $request->content,
                'version' => config('constants.initial_version'),
            ]);

        return redirect()->route('questions.show', $question->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        $question->update(['views_number' => $question->views_number + config('constants.increasing_views_each_request')]);

        $title = $question->title;

        $subtract = Carbon::now()->diff(Carbon::parse($question->created_at));
        $asked = $subtract->y > config('constants.zero')
            ? [
                'y' => $subtract->y,
                'm' => $subtract->m
            ]
            : ($subtract->m > config('constants.zero')
                ? [
                    'm' => $subtract->m,
                    'd' => $subtract->d
                ]
                : ($subtract->d > config('constants.zero')
                    ? [
                        'd' => $subtract->d,
                        'h' => $subtract->h
                    ]
                    : ($subtract->h > config('constants.zero')
                        ? [
                            'h' => $subtract->h,
                            'i' => $subtract->i
                        ]
                        : [
                            'i' => $subtract->i,
                            's' => $subtract->s
                        ])));

        $viewsNumber = $question->views_number;

        $activesNumber = $question->activities_count;

        $votesNumber = $question->votes()->sum('vote');

        $maxContentVersion = $question->contents()->max('version');
        $content = $question->contents()
            ->where('version', $maxContentVersion)
            ->first();

        $comments = $question->comments()
            ->with('user')
            ->get();

        $user = $question->user()->first();

        $answers = $question->answers()
            ->with([
                'user',
                'votes',
                'comments.user',
            ])
            ->get();
        $answers->map(function ($answer) {
            $maxContentVersion = $answer->contents()->max('version');
            $answer->content = $answer->contents()
                ->where('version', $maxContentVersion)
                ->first();

            $answer->sum_votes = $answer->votes->sum('vote');
        });

        return view('post.post', compact(
            'title',
            'asked',
            'viewsNumber',
            'activesNumber',
            'votesNumber',
            'content',
            'comments',
            'user',
            'answers',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        //
    }
}
