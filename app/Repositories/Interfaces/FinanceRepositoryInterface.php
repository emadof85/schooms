<?php
// app/Repositories/Interfaces/FinanceRepositoryInterface.php

namespace App\Repositories\Interfaces;

interface FinanceRepositoryInterface
{
    // Income Methods
    public function getAllIncomes($filters = []);
    public function getIncomeById($incomeId);
    public function createIncome(array $incomeDetails);
    public function updateIncome($incomeId, array $newDetails);
    public function deleteIncome($incomeId);
    public function getIncomeSummary($startDate, $endDate);
    
    // Expense Methods
    public function getAllExpenses($filters = []);
    public function getExpenseById($expenseId);
    public function createExpense(array $expenseDetails);
    public function updateExpense($expenseId, array $newDetails);
    public function deleteExpense($expenseId);
    public function getExpenseSummary($startDate, $endDate);
    
    // Category Management Methods
    public function getAllIncomeCategories($filters = []);
    public function getIncomeCategoryById($categoryId);
    public function createIncomeCategory(array $categoryDetails);
    public function updateIncomeCategory($categoryId, array $newDetails);
    public function deleteIncomeCategory($categoryId);
    
    public function getAllExpenseCategories($filters = []);
    public function getExpenseCategoryById($categoryId);
    public function createExpenseCategory(array $categoryDetails);
    public function updateExpenseCategory($categoryId, array $newDetails);
    public function deleteExpenseCategory($categoryId);
    
    // Deduction/Bonus Methods
    public function getDeductionsBonuses($userId = null, $filters = []);
    public function createDeductionBonus(array $details);
    public function updateDeductionBonus($id, array $newDetails);
    
    // Financial Reports
    public function getFinancialDashboard($period);
    public function getIncomeVsExpenseReport($startDate, $endDate);
    public function getCashFlowStatement($startDate, $endDate);

    // Export Methods (DRY Principle)
    public function getIncomesForExport($startDate, $endDate, $categoryId = null);
    public function getExpensesForExport($startDate, $endDate, $categoryId = null);
    public function getRecordsForExport($type, $startDate, $endDate, $categoryId = null);
}