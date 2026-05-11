<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\IQuizAnswerService;
use App\Services\Interfaces\IQuizAttemptService;
use App\Services\Interfaces\IQuizOptionService;
use App\Services\Interfaces\IQuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        protected IQuizService        $quizService,
        protected IQuizAttemptService $attemptService,
        protected IQuizAnswerService  $answerService,
        protected IQuizOptionService  $optionService,
    ) {}

    public function show(int $quizId)
    {
        $quiz      = $this->quizService->findById($quizId);
        $userId    = session('user_id');
        $canStart  = $this->quizService->canAttempt($userId, $quizId);
        $history   = $this->attemptService->getByQuiz($quizId)->where('user_id', $userId);
        $bestScore = $this->attemptService->getBestScore($userId, $quizId);

        return view('pages.student.quiz', compact('quiz', 'canStart', 'history', 'bestScore'));
    }

    public function start(int $quizId)
    {
        $userId = session('user_id');

        if (!$this->quizService->canAttempt($userId, $quizId)) {
            return back()->withErrors('Bạn đã dùng hết số lần làm bài cho phép.');
        }

        $attempt = $this->attemptService->start($userId, $quizId);

        return redirect()->route('student.quiz.attempt', $attempt->quiz_attempt_id);
    }

    public function attempt(int $attemptId)
    {
        $attempt = $this->attemptService->findById($attemptId);

        if ($attempt->user_id !== session('user_id')) {
            abort(403);
        }

        if ($attempt->submitted_at) {
            return redirect()->route('student.quiz.result', $attemptId);
        }

        $quiz      = $attempt->quiz->load('quizQuestions.quizOptions');
        $timeLimit = $attempt->quiz->time_limit_sec;

        return view('pages.student.quiz-attempt', compact('attempt', 'quiz', 'timeLimit'));
    }

    /**
     * FIX: Xử lý đúng cả single_choice và multiple_choice.
     * multiple_choice gửi: answers[i][selected_option_id][] = [id1, id2, ...]
     * single_choice gửi:   answers[i][selected_option_id]  = id
     */
    public function submit(Request $request, int $attemptId)
    {
        $attempt = $this->attemptService->findById($attemptId);

        if ($attempt->user_id !== session('user_id')) {
            abort(403);
        }

        // Validate linh hoạt: selected_option_id có thể là int hoặc array
        $validated = $request->validate([
            'answers'               => 'required|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.answer_text' => 'nullable|string',
        ]);

        // Thu thập answers thủ công để giữ selected_option_id (cả scalar lẫn array)
        $answers = [];
        foreach ($request->input('answers', []) as $item) {
            $answers[] = [
                'question_id'        => $item['question_id'],
                'selected_option_id' => $item['selected_option_id'] ?? null,
                'answer_text'        => $item['answer_text'] ?? null,
            ];
        }

        $this->answerService->saveBulk($attemptId, $answers);
        $this->attemptService->submit($attemptId);

        return redirect()->route('student.quiz.result', $attemptId);
    }

    public function result(int $attemptId)
    {
        $attempt = $this->attemptService->findById($attemptId);

        if ($attempt->user_id !== session('user_id')) {
            abort(403);
        }

        $answers   = $this->answerService->getByAttempt($attemptId);
        $correct   = $this->answerService->countCorrect($attemptId);
        $total     = $answers->count();
        $bestScore = $this->attemptService->getBestScore(session('user_id'), $attempt->quiz_id);

        return view('pages.student.quiz-result', compact(
            'attempt', 'answers', 'correct', 'total', 'bestScore'
        ));
    }
}

?>