<?php

namespace CCSCalculator\Includes\Calculator;

if (!defined('ABSPATH') && PHP_SAPI !== 'cli') {
    exit;
}

/**
 * CCSEngine — authoritative, server-side re-implementation of the Child Care
 * Subsidy calculation that currently runs as inline JavaScript in
 * includes/Frontend/Shortcode.php.
 *
 * PHASE 1A GOAL: mirror the existing client-side logic EXACTLY (including its
 * quirks) so that server-recomputed figures match what the browser shows. No
 * behavioural/numeric changes are introduced here — correctness fixes are
 * deliberately deferred to Phase 1B and tracked in IMPROVEMENT_PLAN.md.
 *
 * Every method below cites the corresponding Shortcode.php logic so the two
 * stay verifiably in sync.
 *
 * Unit discipline (matches the JS):
 *   - Subsidy percentage functions work in WHOLE-PERCENT units (0..95).
 *   - The per-child cost engine works in FRACTIONS (0..0.95) — callers divide
 *     percentages by 100 before passing them in, exactly as the JS does.
 */
class CCSEngine
{
    /** @var array Plugin policy option (childcare_ccs_policy). */
    private $policy;

    /** @var array Convenience handle to policy['hourly_caps']. */
    private $hourly_caps;

    public function __construct(array $policy = [])
    {
        $this->policy      = $policy;
        $this->hourly_caps = isset($policy['hourly_caps']) && is_array($policy['hourly_caps'])
            ? $policy['hourly_caps']
            : [];
    }

    /* ------------------------------------------------------------------ *
     *  Subsidy percentage logic
     * ------------------------------------------------------------------ */

    /**
     * Standard CCS percentage from family income.
     * Mirrors calculateCCSPercentages() — Shortcode.php lines 1419-1454.
     *
     * @param  float $income Family Adjusted Taxable Income.
     * @return float Standard CCS as a whole percentage in [0, 90].
     */
    public function calculate_standard_pct($income)
    {
        $income = (float) $income;

        $zero             = $this->policy_float('income_zero_threshold', 535279);
        $lowIncomeThresh  = $this->policy_float('low_income_threshold', 85279);

        if ($income <= $lowIncomeThresh) {
            $standard = 90;
        } elseif ($income >= $zero) {
            $standard = 0;
        } else {
            $excess    = $income - $lowIncomeThresh;
            $reduction = $excess / 5000;       // 1 pt per $5,000 (hardcoded in JS)
            $standard  = 90 - $reduction;
        }

        $standard = max(0, min(90, $standard));

        // 33% floor override (Shortcode.php 1450-1457). NOTE: in practice this
        // branch never alters the result because the income window for the
        // floor (<= low + 285000) is exactly where standard first reaches 33;
        // it is mirrored faithfully regardless.
        $income33Threshold = $lowIncomeThresh + (90 - 33) * 5000;
        if ($income > $income33Threshold) {
            $standard = max(0, $standard);
        } elseif ($standard < 33 && $standard > 0) {
            $standard = 33;
        }

        return $standard;
    }

    /**
     * Higher CCS percentage derived from family income (ATI).
     * Mirrors calculateHigherCCSFromATI() — Shortcode.php lines 1261-1298.
     *
     * @param  float $income       Family ATI.
     * @param  float $standard_pct Standard CCS as a whole percentage.
     * @return array{higher: float, eligible: bool} higher is a whole percentage.
     */
    public function calculate_higher_from_ati($income, $standard_pct)
    {
        $ati      = (float) $income;
        $standard = (float) $standard_pct;

        if ($income === '' || $income === null || $ati < 0) {
            return ['higher' => $standard, 'eligible' => false];
        }

        if ($ati >= 367563) {
            $higher = $standard;
            $eligible = false;
        } elseif ($ati <= 143273) {
            $higher = 95;
            $eligible = true;
        } elseif ($ati < 188273) {
            $higher = 95 - (($ati - 143273) / 3000);
            $eligible = true;
        } elseif ($ati < 267563) {
            $higher = 80;
            $eligible = true;
        } elseif ($ati < 357563) {
            $higher = 80 - (($ati - 267563) / 3000);
            $eligible = true;
        } else {
            $higher = 50;
            $eligible = true;
        }

        $higher = max(0, min(95, $higher));

        if ($higher <= $standard) {
            $higher = $standard;
            $eligible = false;
        }

        return ['higher' => $higher, 'eligible' => $eligible];
    }

    /**
     * Higher CCS percentage derived from the standard percentage.
     * Mirrors calculateHigherCCS() — Shortcode.php lines 1203-1258.
     * Retained for parity with the "I know my %" path; the cost engine itself
     * takes explicit percentages and does not call this.
     *
     * @param  float $standard_pct Standard CCS as a whole percentage.
     * @return float Higher CCS as a whole percentage.
     */
    public function calculate_higher_from_standard($standard_pct)
    {
        $s = (float) $standard_pct;

        if ($s >= 78.40) {
            return 95.00;
        }
        if ($s >= 69.41 && $s <= 78.39) {
            $rangeStandard = 78.39 - 69.41;
            $rangeHigher   = 94.98 - 80.01;
            return 80.01 + (($s - 69.41) / $rangeStandard) * $rangeHigher;
        }
        if ($s === 69.40) {
            return 80.00;
        }
        if ($s >= 53.55 && $s <= 69.39) {
            return 80.00;
        }
        if ($s === 53.54) {
            return 79.99;
        }
        if ($s >= 35.55 && $s <= 53.53) {
            $rangeStandard = 53.53 - 35.55;
            $rangeHigher   = 79.98 - 50.01;
            return 50.01 + (($s - 35.55) / $rangeStandard) * $rangeHigher;
        }
        if ($s >= 33.55 && $s <= 35.54) {
            return 50.00;
        }
        if ($s <= 33.53) {
            return $s;
        }
        return 0;
    }

    /* ------------------------------------------------------------------ *
     *  Age helper
     * ------------------------------------------------------------------ */

    /**
     * Whole-year age from a date-of-birth string (YYYY-MM-DD).
     * Mirrors getAge() — Shortcode.php line 1779 — for all valid past dates
     * (returns the same integer the JS produces). Uses a robust calendar diff
     * rather than the JS epoch trick; results are identical for real DOBs.
     *
     * @param  string      $dob
     * @param  string|null $as_of Reference date (for testability); defaults to now.
     * @return int
     */
    public function get_age($dob, $as_of = null)
    {
        if (empty($dob)) {
            return 0;
        }

        try {
            $birth = new \DateTime($dob);
            $now   = new \DateTime($as_of ?: 'now');
        } catch (\Exception $e) {
            return 0;
        }

        if ($birth > $now) {
            return 0;
        }

        return (int) $now->diff($birth)->y;
    }

    /* ------------------------------------------------------------------ *
     *  Per-child cost engine
     * ------------------------------------------------------------------ */

    /**
     * Run the full calculation for a set of children.
     * Mirrors calculateCCS() — Shortcode.php lines 1783-1912.
     *
     * Expected $input keys:
     *   knows_ccs       bool    "I know my CCS %" mode.
     *   income          float   Family ATI.
     *   activity_hours  float   Already parsed (e.g. 'none' -> 0, '49' -> 49).
     *   withholding_pct float   Fraction, e.g. 0.05.
     *   is_atsi         bool
     *   standard_pct    float   Fraction (0..0.90). Used in knows_ccs mode, or
     *                           ignored and recomputed from income otherwise.
     *   higher_pct      float   Fraction (0..0.95). As above.
     *   children        array   Each: dob, hours_per_day, fee_per_day,
     *                           days_week1, days_week2.
     *
     * @return array{children: array, totals: array, standard_pct: float,
     *               higher_pct: float, ccs_hours_per_fortnight: int}
     */
    public function calculate(array $input)
    {
        $income         = (float) ($input['income'] ?? 0);
        $activityHours  = (float) ($input['activity_hours'] ?? 0);
        $withholdingPct = isset($input['withholding_pct']) ? (float) $input['withholding_pct'] : 0.05;
        $isATSI         = !empty($input['is_atsi']);
        $knowsCCS       = !empty($input['knows_ccs']);

        // Resolve standard/higher percentages (fractions), mirroring the
        // yes-mode vs no-mode branch in Shortcode.php 1797-1808 / 1419-1474.
        if ($knowsCCS) {
            $standardCCSPct = isset($input['standard_pct']) ? (float) $input['standard_pct'] : 0.0;
            $higherCCSPct   = isset($input['higher_pct']) ? (float) $input['higher_pct'] : 0.0;
        } else {
            // The browser computes the higher rate from the UN-rounded standard,
            // then stores BOTH percentages in DOM fields via toFixed(2) and reads
            // them back (Shortcode.php 1466, 1473-1474, 1806-1807). We replicate
            // that 2-decimal round-trip so the server matches the browser exactly.
            $standardWhole  = $this->calculate_standard_pct($income);
            $hccs           = $this->calculate_higher_from_ati($income, $standardWhole);
            $standardCCSPct = round($standardWhole, 2) / 100;
            // JS fallback for the higher field is 0.95 (Shortcode.php 1807).
            $higherCCSPct   = $hccs['higher'] > 0 ? (round($hccs['higher'], 2) / 100) : 0.95;
        }

        // 3-Day Guarantee hours (Shortcode.php 1810-1816).
        $ccsHoursPerFortnight = 72;
        if ($activityHours > 48 || $isATSI) {
            $ccsHoursPerFortnight = 100;
        }
        $ccsHoursPerWeek = $ccsHoursPerFortnight / 2;

        $higherCCSThreshold = $this->policy_float('higher_ccs_threshold', 367563);

        $asOf = $input['as_of'] ?? null;

        $children = [];
        $childIndex = 0;

        foreach (($input['children'] ?? []) as $child) {
            $dob         = $child['dob'] ?? '';
            $careType    = $child['care_type'] ?? 'cbdc';
            $hoursPerDay = (float) ($child['hours_per_day'] ?? 0);
            $feePerDay   = (float) ($child['fee_per_day'] ?? 0);
            $daysWeek1   = (int) ($child['days_week1'] ?? 0);
            $daysWeek2   = (int) ($child['days_week2'] ?? 0);

            $week1Fee     = $daysWeek1 * $feePerDay;
            $week2Fee     = $daysWeek2 * $feePerDay;
            $fortnightFee = $week1Fee + $week2Fee;

            $age = $this->get_age($dob, $asOf);

            // Multi-child higher-rate eligibility (Shortcode.php 1834).
            $isEligibleForHigherCCS = ($childIndex >= 1 && $age <= 5 && $income < $higherCCSThreshold);
            $ccs_pct = $isEligibleForHigherCCS ? $higherCCSPct : $standardCCSPct;

            // Hourly fee + cap by selected care type and age (mirrors getHourlyCap
            // in Shortcode.php). 'cbdc' default preserves the prior behaviour.
            $hourlyFee = $hoursPerDay > 0 ? $feePerDay / $hoursPerDay : 0;
            $cap = $this->hourly_cap($careType, $age);

            $effectiveHourlyRate = min($hourlyFee, $cap);
            $hourlyCCSAmount     = $effectiveHourlyRate * $ccs_pct;
            $weeklyCCSEntitlement = $hourlyCCSAmount * $ccsHoursPerWeek;

            $week1Hours = $daysWeek1 * $hoursPerDay;
            $week2Hours = $daysWeek2 * $hoursPerDay;

            $week1SubBeforeWithholding = min($week1Hours * $hourlyCCSAmount, $weeklyCCSEntitlement, $week1Fee);
            $week2SubBeforeWithholding = min($week2Hours * $hourlyCCSAmount, $weeklyCCSEntitlement, $week2Fee);

            $week1Withholding = $week1SubBeforeWithholding * $withholdingPct;
            $week2Withholding = $week2SubBeforeWithholding * $withholdingPct;

            $week1Sub = $week1SubBeforeWithholding - $week1Withholding;
            $week2Sub = $week2SubBeforeWithholding - $week2Withholding;
            $fortnightSub = $week1Sub + $week2Sub;
            $fortnightSubBeforeWithholding = $week1SubBeforeWithholding + $week2SubBeforeWithholding;

            $week1OutOfPocket = max(0, $week1Fee - $week1Sub);
            $week2OutOfPocket = max(0, $week2Fee - $week2Sub);
            $outPocket = $week1OutOfPocket + $week2OutOfPocket;

            // after-EOY floored at 0 (mirrors the JS). Mathematically this equals
            // weekFee - subBeforeWithholding which is always >= 0 (the 3-way min
            // caps the subsidy at the fee), so the floor is defensive.
            $week1AfterEOY = max(0, $week1OutOfPocket - $week1Withholding);
            $week2AfterEOY = max(0, $week2OutOfPocket - $week2Withholding);

            $children[] = [
                'dob'                           => $dob,
                'age'                           => $age,
                'hoursPerDay'                   => $hoursPerDay,
                'feePerDay'                     => $feePerDay,
                'daysWeek1'                     => $daysWeek1,
                'daysWeek2'                     => $daysWeek2,
                'week1Fee'                      => $week1Fee,
                'week2Fee'                      => $week2Fee,
                'fortnightFee'                  => $fortnightFee,
                'week1Sub'                      => $week1Sub,
                'week2Sub'                      => $week2Sub,
                'fortnightSub'                  => $fortnightSub,
                'fortnightSubBeforeWithholding' => $fortnightSubBeforeWithholding,
                'week1SubBeforeWithholding'     => $week1SubBeforeWithholding,
                'week2SubBeforeWithholding'     => $week2SubBeforeWithholding,
                'outPocket'                     => $outPocket,
                'week1Withholding'              => $week1Withholding,
                'week2Withholding'              => $week2Withholding,
                'week1AfterEOY'                 => $week1AfterEOY,
                'week2AfterEOY'                 => $week2AfterEOY,
                'ccs_pct'                       => $ccs_pct,
                'isHigherCCS'                   => $isEligibleForHigherCCS,
                'hourlyFee'                     => $hourlyFee,
                'hourlyCap'                     => $cap,
                'hourlyCCSAmount'               => $hourlyCCSAmount,
            ];

            $childIndex++;
        }

        return [
            'children'                => $children,
            'totals'                  => $this->sum_totals($children),
            'standard_pct'            => $standardCCSPct,
            'higher_pct'              => $higherCCSPct,
            'ccs_hours_per_fortnight' => $ccsHoursPerFortnight,
        ];
    }

    /**
     * Period scaling multipliers — mirrors renderSummary() multipliers
     * (Shortcode.php line 1917). Applies to the per-fortnight totals.
     *
     * @param  array  $totals Per-fortnight totals from calculate().
     * @param  string $period week|fortnight|month|year
     * @return array
     */
    public function scale_totals(array $totals, $period = 'fortnight')
    {
        $multipliers = ['week' => 0.5, 'fortnight' => 1, 'month' => 26 / 12, 'year' => 26];
        $mult = $multipliers[$period] ?? 1;

        $scaled = [];
        foreach ($totals as $key => $value) {
            $scaled[$key] = $value * $mult;
        }
        return $scaled;
    }

    /* ------------------------------------------------------------------ *
     *  Internals
     * ------------------------------------------------------------------ */

    private function sum_totals(array $children)
    {
        $totals = [
            'fortnightFee'                  => 0.0,
            'fortnightSub'                  => 0.0,
            'fortnightSubBeforeWithholding' => 0.0,
            'outPocket'                     => 0.0,
            'withholding'                   => 0.0,
        ];

        foreach ($children as $c) {
            $totals['fortnightFee']                  += $c['fortnightFee'];
            $totals['fortnightSub']                  += $c['fortnightSub'];
            $totals['fortnightSubBeforeWithholding'] += $c['fortnightSubBeforeWithholding'];
            $totals['outPocket']                     += $c['outPocket'];
            $totals['withholding']                   += $c['week1Withholding'] + $c['week2Withholding'];
        }

        return $totals;
    }

    /**
     * Hourly CCS rate cap by care type + age, using admin-configured caps.
     * Mirrors getHourlyCap() in Shortcode.php. 'cbdc' (default) preserves the
     * prior behaviour: below school age -> Centre-Based cap, school age -> OSHC
     * school-age cap (identical $ value in official policy).
     *
     * @param  string $care_type cbdc|fdc|oshc|ihc
     * @param  int    $age
     * @return float
     */
    private function hourly_cap($care_type, $age)
    {
        $belowSchool = $age < 6;

        switch ($care_type) {
            case 'fdc':
                return $this->cap_float('family_day_care_all', 13.56);
            case 'oshc':
                return $belowSchool
                    ? $this->cap_float('oshc_below_school_age', 14.63)
                    : $this->cap_float('oshc_school_age', 12.81);
            case 'ihc':
                return $this->cap_float('in_home_family', 39.80);
            case 'cbdc':
            default:
                return $belowSchool
                    ? $this->cap_float('centre_based_day_care', 14.63)
                    : $this->cap_float('oshc_school_age', 12.81);
        }
    }

    private function policy_float($key, $default)
    {
        if (isset($this->policy[$key]) && $this->policy[$key] !== '') {
            return (float) $this->policy[$key];
        }
        return (float) $default;
    }

    private function cap_float($key, $default)
    {
        if (isset($this->hourly_caps[$key]) && $this->hourly_caps[$key] !== '') {
            return (float) $this->hourly_caps[$key];
        }
        return (float) $default;
    }
}
