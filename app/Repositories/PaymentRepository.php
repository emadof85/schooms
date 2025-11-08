<?php
// app/Repositories/PaymentRepository.php

namespace App\Repositories;

use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;
use App\Models\PaymentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function getAllPayments($filters = [])
    {
        $query = Payment::with('myClass');
        
        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }
        
        if (isset($filters['class_id'])) {
            $query->where('my_class_id', $filters['class_id']);
        }
        
        return $query->latest()->get();
    }
    
    public function getPaymentById($paymentId)
    {
        return Payment::with(['myClass', 'paymentRecords.student'])->findOrFail($paymentId);
    }
    
    public function createPayment(array $paymentDetails)
    {
        $paymentDetails['ref_no'] = $this->generatePaymentRef();
        return Payment::create($paymentDetails);
    }
    
    public function updatePayment($paymentId, array $newDetails)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update($newDetails);
        return $payment;
    }
    
    public function deletePayment($paymentId)
    {
        DB::transaction(function() use ($paymentId) {
            PaymentRecord::where('payment_id', $paymentId)->delete();
            Payment::destroy($paymentId);
        });
    }
    
    public function getPaymentRecords($paymentId = null, $filters = [])
    {
        $query = PaymentRecord::with(['payment', 'student']);
        
        if ($paymentId) {
            $query->where('payment_id', $paymentId);
        }
        
        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }
        
        if (isset($filters['paid'])) {
            $query->where('paid', $filters['paid']);
        }
        
        return $query->latest()->paginate(50);
    }
    
    public function createPaymentRecord(array $recordDetails)
    {
        $recordDetails['ref_no'] = $this->generatePaymentRecordRef();
        return PaymentRecord::create($recordDetails);
    }
    
    public function updatePaymentRecord($recordId, array $newDetails)
    {
        $record = PaymentRecord::findOrFail($recordId);
        $record->update($newDetails);
        return $record;
    }
    
    public function getStudentPaymentHistory($studentId)
    {
        return PaymentRecord::where('student_id', $studentId)
            ->with('payment')
            ->latest()
            ->get();
    }
    
    public function getFeeCollectionReport($year, $month = null)
    {
        $query = PaymentRecord::where('year', $year)
            ->where('paid', 1)
            ->with(['payment', 'student']);
            
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        
        $records = $query->get();
        
        return [
            'total_collected' => $records->sum('amt_paid'),
            'total_transactions' => $records->count(),
            'by_payment_type' => $records->groupBy('payment_id')->map(function($items, $paymentId) {
                return [
                    'payment_title' => $items->first()->payment->title,
                    'total' => $items->sum('amt_paid'),
                    'count' => $items->count()
                ];
            })
        ];
    }
    
    public function getOutstandingPayments($filters = [])
    {
        $query = PaymentRecord::where('paid', 0)
            ->orWhere('balance', '>', 0)
            ->with(['payment', 'student']);
            
        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }
        
        if (isset($filters['class_id'])) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }
        
        return $query->get();
    }
    
    private function generatePaymentRef()
    {
        $count = Payment::whereYear('created_at', now()->year)->count() + 1;
        return 'PAY-' . now()->format('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
    
    private function generatePaymentRecordRef()
    {
        $count = PaymentRecord::whereYear('created_at', now()->year)->count() + 1;
        return 'REC-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}