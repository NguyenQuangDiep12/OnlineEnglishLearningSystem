<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\Interfaces\IEnrollmentService;
use App\Services\Interfaces\IPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        protected IPaymentService    $paymentService,
        protected IEnrollmentService $enrollmentService,
    ) {}

    public function checkout(int $courseId)
    {
        $userId = session('user_id');
        $course = Course::findOrFail($courseId);

        if ($this->enrollmentService->isEnrolled($userId, $courseId)) {
            return redirect()->route('student.dashboard')
                ->with('info', 'Bạn đã đăng ký khóa học này rồi.');
        }

        if ($course->price == 0) {
            $this->enrollmentService->enroll($userId, $courseId);
            return redirect()->route('student.dashboard')
                ->with('success', 'Đã đăng ký khóa học miễn phí thành công!');
        }
        return view('pages.checkout', compact('course'));
    }

    public function createPayment(Request $request, int $courseId)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:credit_card,momo,vnpay,bank_transfer,zalopay',
        ]);

        $course = Course::findOrFail($courseId);

        $payment = $this->paymentService->createPayment(
            session('user_id'),
            $courseId,
            [
                'amount'          => $course->price,
                'payment_method'  => $data['payment_method'],
                'transaction_ref' => 'TXN-' . strtoupper(Str::random(12)),
            ]
        );

        // Mock: confirm ngay (thực tế sẽ redirect sang cổng thanh toán)
        $this->paymentService->confirmPayment($payment->transaction_ref);
        $this->enrollmentService->enroll(session('user_id'), $courseId);

        return redirect()->route('student.dashboard')
            ->with('success', 'Thanh toán thành công! Bắt đầu học ngay.');
    }

    public function callback(Request $request)
    {
        $ref    = $request->query('transaction_ref') ?? $request->query('vnp_TxnRef');
        $status = $request->query('status') ?? $request->query('vnp_ResponseCode');

        if (!$ref) {
            return redirect()->route('courses.index')->withErrors('Thiếu mã giao dịch.');
        }

        if ($status === '00' || $status === 'success') {
            $payment = $this->paymentService->confirmPayment($ref);
            $this->enrollmentService->enroll($payment->user_id, $payment->course_id);
            return redirect()->route('student.dashboard')
                ->with('success', 'Thanh toán thành công!');
        }

        return redirect()->route('courses.index')
            ->withErrors('Thanh toán thất bại hoặc bị huỷ.');
    }

    public function history()
    {
        $payments = $this->paymentService->getByUser(session('user_id'));
        return view('pages.student.payment-history', compact('payments'));
    }
}

?>