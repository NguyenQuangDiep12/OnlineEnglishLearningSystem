<?php

namespace App\Services;

use App\Models\QuizAnswer;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Services\Interfaces\IQuizAnswerService;
use Illuminate\Support\Collection;

class QuizAnswerService implements IQuizAnswerService
{
    public function saveAnswer(int $attemptId, int $questionId, array $data): QuizAnswer
    {
        $isCorrect = $this->checkCorrect($questionId, $data);

        $question = QuizQuestion::find($questionId);
        $points   = $isCorrect ? ($question->points ?? 1) : 0;

        return QuizAnswer::updateOrCreate(
            ['quiz_attempt_id' => $attemptId, 'question_id' => $questionId],
            [
                'selected_option_id' => isset($data['selected_option_id']) && !is_array($data['selected_option_id'])
                    ? $data['selected_option_id']
                    : null,
                'answer_text'        => $data['answer_text'] ?? null,
                'is_correct'         => $isCorrect,
                'points_earned'      => $points,
            ]
        );
    }

    /**
     * Lưu bulk answers.
     * FIX: xử lý multiple_choice (selected_option_id là mảng).
     * Với multiple_choice, lưu một bản ghi tổng hợp (is_correct = tất cả đúng).
     */
    public function saveBulk(int $attemptId, array $answers): Collection
    {
        $saved = collect();

        foreach ($answers as $answer) {
            $questionId = $answer['question_id'];
            $question   = QuizQuestion::find($questionId);

            if (!$question) continue;

            // Trường hợp multiple_choice: selected_option_id là mảng
            if (
                $question->question_type === 'multiple_choice' &&
                isset($answer['selected_option_id']) &&
                is_array($answer['selected_option_id'])
            ) {
                $selectedIds   = array_filter($answer['selected_option_id']); // bỏ null/empty
                $correctIds    = QuizOption::where('question_id', $questionId)
                    ->where('is_correct', true)
                    ->pluck('quiz_option_id')
                    ->sort()
                    ->values()
                    ->toArray();

                sort($selectedIds);

                $isCorrect = !empty($selectedIds) && $selectedIds == $correctIds;
                $points    = $isCorrect ? ($question->points ?? 1) : 0;

                // Lưu dưới dạng answer_text = JSON danh sách ids đã chọn
                $record = QuizAnswer::updateOrCreate(
                    ['quiz_attempt_id' => $attemptId, 'question_id' => $questionId],
                    [
                        'selected_option_id' => null,
                        'answer_text'        => json_encode($selectedIds),
                        'is_correct'         => $isCorrect,
                        'points_earned'      => $points,
                    ]
                );

                $saved->push($record);
                continue;
            }

            // Trường hợp thông thường
            $saved->push($this->saveAnswer($attemptId, $questionId, $answer));
        }

        return $saved;
    }

    public function getByAttempt(int $attemptId): Collection
    {
        return QuizAnswer::with(['question', 'selectedOption'])
            ->where('quiz_attempt_id', $attemptId)
            ->get();
    }

    public function evaluate(int $answerId): QuizAnswer
    {
        $answer = QuizAnswer::with('question')->findOrFail($answerId);

        $isCorrect = $this->checkCorrect($answer->question_id, [
            'selected_option_id' => $answer->selected_option_id,
            'answer_text'        => $answer->answer_text,
        ]);

        $answer->update([
            'is_correct'    => $isCorrect,
            'points_earned' => $isCorrect ? ($answer->question->points ?? 1) : 0,
        ]);

        return $answer;
    }

    public function countCorrect(int $attemptId): int
    {
        return QuizAnswer::where('quiz_attempt_id', $attemptId)
            ->where('is_correct', true)
            ->count();
    }

    // ── Private helpers ────────────────────────────────────────

    private function checkCorrect(int $questionId, array $data): bool
    {
        // selected_option_id là mảng (multiple_choice)
        if (isset($data['selected_option_id']) && is_array($data['selected_option_id'])) {
            $selectedIds = array_filter($data['selected_option_id']);
            sort($selectedIds);

            $correctIds = QuizOption::where('question_id', $questionId)
                ->where('is_correct', true)
                ->pluck('quiz_option_id')
                ->sort()
                ->values()
                ->toArray();

            return !empty($selectedIds) && $selectedIds == $correctIds;
        }

        // selected_option_id đơn
        if (!empty($data['selected_option_id'])) {
            return QuizOption::where('quiz_option_id', $data['selected_option_id'])
                ->where('is_correct', true)
                ->exists();
        }

        // fill_blank: so sánh text
        if (!empty($data['answer_text'])) {
            return QuizOption::where('question_id', $questionId)
                ->where('is_correct', true)
                ->whereRaw('LOWER(option_text) = LOWER(?)', [$data['answer_text']])
                ->exists();
        }

        return false;
    }
}

?>