<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanType;
use Carbon\Carbon;

class LoanService
{
    public static function calculateEMI(float $principal, float $annualRate, int $termMonths): float
    {
        if ($annualRate <= 0) {
            return round($principal / $termMonths, 2);
        }

        $monthlyRate = ($annualRate / 100) / 12;
        $emi = $principal * $monthlyRate * pow(1 + $monthlyRate, $termMonths) 
            / (pow(1 + $monthlyRate, $termMonths) - 1);
        
        return round($emi, 2);
    }

    public static function calculateFlatInterest(float $principal, float $annualRate, int $termMonths): float
    {
        $totalInterest = $principal * ($annualRate / 100) * ($termMonths / 12);
        return round($totalInterest, 2);
    }

    public static function calculateTotalRepayment(float $principal, float $totalInterest): float
    {
        return round($principal + $totalInterest, 2);
    }

    public static function generateRepaymentSchedule(Loan $loan): array
    {
        $schedule = [];
        $principal = $loan->loan_amount;
        $rate = $loan->interest_rate;
        $term = $loan->loan_term_months;
        $rateType = $loan->rate_type;

        $disbursementDate = Carbon::parse($loan->disbursement_date ?? $loan->application_date);
        $firstInstallmentDate = $loan->first_installment_date 
            ? Carbon::parse($loan->first_installment_date) 
            : $disbursementDate->copy()->addMonth();

        if ($rateType === 'flat') {
            $schedule = self::generateFlatRateSchedule($principal, $rate, $term, $firstInstallmentDate);
        } elseif ($rateType === 'reducing_balance') {
            $schedule = self::generateReducingBalanceSchedule($principal, $rate, $term, $firstInstallmentDate);
        } else {
            $schedule = self::generateSimpleSchedule($principal, $rate, $term, $firstInstallmentDate);
        }

        return $schedule;
    }

    protected static function generateFlatRateSchedule(float $principal, float $rate, int $term, Carbon $startDate): array
    {
        $schedule = [];
        $totalInterest = self::calculateFlatInterest($principal, $rate, $term);
        $totalRepayment = self::calculateTotalRepayment($principal, $totalInterest);
        $emi = round($totalRepayment / $term, 2);
        $monthlyPrincipal = round($principal / $term, 2);
        $monthlyInterest = round($totalInterest / $term, 2);
        $balance = $totalRepayment;

        for ($i = 1; $i <= $term; $i++) {
            $balance -= $emi;
            if ($i === $term) {
                $balance = 0;
            }

            $schedule[] = [
                'installment_number' => $i,
                'due_date' => $startDate->copy()->addMonths($i - 1)->format('Y-m-d'),
                'emi_amount' => $emi,
                'principal_amount' => $monthlyPrincipal,
                'interest_amount' => $monthlyInterest,
                'balance_amount' => max(0, round($balance, 2)),
            ];
        }

        return $schedule;
    }

    protected static function generateReducingBalanceSchedule(float $principal, float $rate, int $term, Carbon $startDate): array
    {
        $schedule = [];
        $monthlyRate = ($rate / 100) / 12;
        $emi = self::calculateEMI($principal, $rate, $term);
        $balance = $principal;
        $totalInterest = 0;

        for ($i = 1; $i <= $term; $i++) {
            $interestPayment = round($balance * $monthlyRate, 2);
            $principalPayment = round($emi - $interestPayment, 2);
            
            if ($i === $term) {
                $principalPayment = $balance;
                $emi = round($principalPayment + $interestPayment, 2);
                $balance = 0;
            } else {
                $balance = round($balance - $principalPayment, 2);
            }

            $totalInterest += $interestPayment;

            $schedule[] = [
                'installment_number' => $i,
                'due_date' => $startDate->copy()->addMonths($i - 1)->format('Y-m-d'),
                'emi_amount' => $emi,
                'principal_amount' => $principalPayment,
                'interest_amount' => $interestPayment,
                'balance_amount' => max(0, round($balance, 2)),
            ];
        }

        return $schedule;
    }

    protected static function generateSimpleSchedule(float $principal, float $rate, int $term, Carbon $startDate): array
    {
        $schedule = [];
        $totalInterest = self::calculateFlatInterest($principal, $rate, $term);
        $totalRepayment = self::calculateTotalRepayment($principal, $totalInterest);
        $monthlyPrincipal = round($principal / $term, 2);
        $monthlyInterest = round($totalInterest / $term, 2);
        $emi = round($monthlyPrincipal + $monthlyInterest, 2);
        $balance = $totalRepayment;

        for ($i = 1; $i <= $term; $i++) {
            $balance -= $emi;
            if ($i === $term) {
                $balance = 0;
            }

            $schedule[] = [
                'installment_number' => $i,
                'due_date' => $startDate->copy()->addMonths($i - 1)->format('Y-m-d'),
                'emi_amount' => $emi,
                'principal_amount' => $monthlyPrincipal,
                'interest_amount' => $monthlyInterest,
                'balance_amount' => max(0, round($balance, 2)),
            ];
        }

        return $schedule;
    }

    public static function createLoanApplication(array $data, int $groupId): Loan
    {
        $loanType = LoanType::findOrFail($data['loan_type_id']);
        
        $principal = $data['loan_amount'];
        $rate = $data['interest_rate'] ?? $loanType->rate_percentage;
        $rateType = $data['rate_type'] ?? $loanType->rate_type;
        $term = $data['loan_term_months'];

        if ($rateType === 'reducing_balance') {
            $emi = self::calculateEMI($principal, $rate, $term);
            $totalRepayment = round($emi * $term, 2);
            $totalInterest = round($totalRepayment - $principal, 2);
        } else {
            $totalInterest = self::calculateFlatInterest($principal, $rate, $term);
            $totalRepayment = self::calculateTotalRepayment($principal, $totalInterest);
            $emi = round($totalRepayment / $term, 2);
        }

        $applicationDate = $data['application_date'] ?? now()->format('Y-m-d');
        $firstInstallmentDate = $data['first_installment_date'] ?? null;

        $loan = Loan::create([
            'group_id' => $groupId,
            'member_id' => $data['member_id'],
            'loan_type_id' => $data['loan_type_id'],
            'calendar_year_id' => $data['calendar_year_id'],
            'loan_number' => self::generateLoanNumber(),
            'loan_amount' => $principal,
            'interest_rate' => $rate,
            'rate_type' => $rateType,
            'loan_term_months' => $term,
            'total_interest_amount' => $totalInterest,
            'total_repayment_amount' => $totalRepayment,
            'monthly_installment' => $emi,
            'amount_paid' => 0,
            'amount_remaining' => $totalRepayment,
            'application_date' => $applicationDate,
            'first_installment_date' => $firstInstallmentDate,
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);

        return $loan;
    }

    public static function generateLoanNumber(): string
    {
        do {
            $number = 'LN-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Loan::where('loan_number', $number)->exists());

        return $number;
    }

    public static function disburseLoan(Loan $loan, array $data): void
    {
        $loan->update([
            'status' => 'disbursed',
            'disbursement_date' => $data['disbursement_date'] ?? now()->format('Y-m-d'),
            'first_installment_date' => $data['first_installment_date'] ?? now()->addMonth()->format('Y-m-d'),
            'disbursed_by' => $data['disbursed_by'],
            'disbursement_notes' => $data['notes'] ?? null,
        ]);

        $schedule = self::generateRepaymentSchedule($loan);

        foreach ($schedule as $item) {
            LoanRepayment::create([
                'loan_id' => $loan->id,
                'member_id' => $loan->member_id,
                'installment_number' => $item['installment_number'],
                'due_date' => $item['due_date'],
                'emi_amount' => $item['emi_amount'],
                'principal_amount' => $item['principal_amount'],
                'interest_amount' => $item['interest_amount'],
                'balance_amount' => $item['balance_amount'],
                'payment_status' => 'pending',
            ]);
        }
    }

    public static function updateOverdueRepayments(): void
    {
        LoanRepayment::where('payment_status', 'pending')
            ->where('due_date', '<', now()->subDays(7))
            ->update(['payment_status' => 'overdue']);
    }
}
