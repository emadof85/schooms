<?php
// app/Repositories/Interfaces/PaymentRepositoryInterface.php

namespace App\Repositories\Interfaces;

interface PaymentRepositoryInterface
{
    public function getAllPayments($filters = []);
    public function getPaymentById($paymentId);
    public function createPayment(array $paymentDetails);
    public function updatePayment($paymentId, array $newDetails);
    public function deletePayment($paymentId);
    
    public function getPaymentRecords($paymentId = null, $filters = []);
    public function createPaymentRecord(array $recordDetails);
    public function updatePaymentRecord($recordId, array $newDetails);
    public function getStudentPaymentHistory($studentId);
    
    public function getFeeCollectionReport($year, $month = null);
    public function getOutstandingPayments($filters = []);
}