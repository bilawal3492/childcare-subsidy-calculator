<?php
/**
 * Standalone test harness for CCSEngine (Phase 1A).
 *
 * Runs without WordPress or PHPUnit:  php tests/test-ccs-engine.php
 *
 * These tests LOCK the current ("mirror exactly") behaviour. Expected values
 * are hand-computed from the Shortcode.php JS spec using clean inputs so the
 * arithmetic is unambiguous. If a Phase 1B fix intentionally changes a number,
 * the corresponding expected value here must be updated in the same commit.
 */

require __DIR__ . '/../includes/Calculator/CCSEngine.php';

use CCSCalculator\Includes\Calculator\CCSEngine;

$AS_OF = '2026-06-26';   // fixed reference date so ages are deterministic

$pass = 0;
$fail = 0;
$failures = [];

function approx($a, $b, $eps = 0.01) {
    return abs($a - $b) <= $eps;
}

function check($label, $got, $expected, $eps = 0.01) {
    global $pass, $fail, $failures;
    $ok = is_bool($expected) ? ($got === $expected) : approx($got, $expected, $eps);
    if ($ok) {
        $pass++;
    } else {
        $fail++;
        $gotStr = is_bool($got) ? ($got ? 'true' : 'false') : $got;
        $expStr = is_bool($expected) ? ($expected ? 'true' : 'false') : $expected;
        $failures[] = "FAIL: {$label}\n      got={$gotStr}  expected={$expStr}";
    }
}

$engine = new CCSEngine([]);   // empty policy -> use JS fallbacks (14.63 / 12.81 / 535279 / 85279 / 367563)

/* ============================================================= *
 *  1. Standard percentage from income (calculate_standard_pct)
 * ============================================================= */
check('standard @ 50000 (<= low) = 90',      $engine->calculate_standard_pct(50000), 90.0);
check('standard @ 85279 (= low) = 90',       $engine->calculate_standard_pct(85279), 90.0);
check('standard @ 185279 = 70',              $engine->calculate_standard_pct(185279), 70.0);
check('standard @ 285279 = 50',              $engine->calculate_standard_pct(285279), 50.0);
check('standard @ 535279 (= zero) = 0',      $engine->calculate_standard_pct(535279), 0.0);
check('standard @ 600000 (> zero) = 0',      $engine->calculate_standard_pct(600000), 0.0);

/* ============================================================= *
 *  2. Higher percentage from ATI (calculate_higher_from_ati)
 * ============================================================= */
$h = $engine->calculate_higher_from_ati(100000, 90);
check('higher ATI @ 100000 = 95',            $h['higher'], 95.0);
check('higher ATI @ 100000 eligible? (95<=90 std? no, 95>90) ', $h['eligible'], true);

$h = $engine->calculate_higher_from_ati(170273, 70);
check('higher ATI @ 170273 = 86',            $h['higher'], 86.0);

$h = $engine->calculate_higher_from_ati(200000, 60);
check('higher ATI @ 200000 = 80',            $h['higher'], 80.0);

$h = $engine->calculate_higher_from_ati(297563, 50);
check('higher ATI @ 297563 = 70',            $h['higher'], 70.0);

$h = $engine->calculate_higher_from_ati(360000, 40);
check('higher ATI @ 360000 = 50',            $h['higher'], 50.0);

$h = $engine->calculate_higher_from_ati(400000, 27.0558);
check('higher ATI @ 400000 (>=367563) = standard (not eligible)', $h['higher'], 27.0558);
check('higher ATI @ 400000 eligible=false', $h['eligible'], false);

/* ============================================================= *
 *  3. get_age (deterministic via fixed reference date)
 * ============================================================= */
check('age born 2023-01-01 as of 2026-06-26 = 3', $engine->get_age('2023-01-01', $AS_OF), 3);
check('age born 2018-01-01 as of 2026-06-26 = 8', $engine->get_age('2018-01-01', $AS_OF), 8);
check('age empty dob = 0',                         $engine->get_age('', $AS_OF), 0);
check('age future dob = 0',                        $engine->get_age('2030-01-01', $AS_OF), 0);
// Calendar-age edge cases (birthday boundary + leap year), as of 2026-06-26
check('age birthday today (2020-06-26) = 6',       $engine->get_age('2020-06-26', $AS_OF), 6);
check('age birthday tomorrow (2020-06-27) = 5',    $engine->get_age('2020-06-27', $AS_OF), 5);
check('age leap-year dob (2020-02-29) = 6',        $engine->get_age('2020-02-29', $AS_OF), 6);

/* ============================================================= *
 *  4. Single child, low income, 90%, 72h fortnight
 *     income=50000, age3, 10h/day, $120/day, 5+5 days, wh=5%
 * ============================================================= */
$r = $engine->calculate([
    'income' => 50000, 'activity_hours' => 0, 'withholding_pct' => 0.05, 'is_atsi' => false,
    'as_of' => $AS_OF,
    'children' => [
        ['dob' => '2023-01-01', 'hours_per_day' => 10, 'fee_per_day' => 120, 'days_week1' => 5, 'days_week2' => 5],
    ],
]);
$c = $r['children'][0];
check('A: ccs_hours_per_fortnight = 72',     $r['ccs_hours_per_fortnight'], 72);
check('A: standard_pct = 0.90',              $r['standard_pct'], 0.90);
check('A: child ccs_pct = 0.90',             $c['ccs_pct'], 0.90);
check('A: hourlyCap = 14.63',                $c['hourlyCap'], 14.63);
check('A: fortnightFee = 1200',              $c['fortnightFee'], 1200.0);
check('A: week1 sub-before-wh = 388.8',      $c['week1SubBeforeWithholding'], 388.8);
check('A: fortnightSub = 738.72',            $c['fortnightSub'], 738.72);
check('A: outPocket = 461.28',               $c['outPocket'], 461.28);
check('A: week1AfterEOY = 211.20',           $c['week1AfterEOY'], 211.20);
check('A: total fortnightSub = 738.72',      $r['totals']['fortnightSub'], 738.72);

/* ============================================================= *
 *  5. Two children — second gets higher rate (95%)
 *     income=85279 (std 90, higher 95), both age3, 10h/day, $100/day, 5+5, wh=0
 * ============================================================= */
$r = $engine->calculate([
    'income' => 85279, 'activity_hours' => 0, 'withholding_pct' => 0, 'is_atsi' => false,
    'as_of' => $AS_OF,
    'children' => [
        ['dob' => '2023-01-01', 'hours_per_day' => 10, 'fee_per_day' => 100, 'days_week1' => 5, 'days_week2' => 5],
        ['dob' => '2023-01-01', 'hours_per_day' => 10, 'fee_per_day' => 100, 'days_week1' => 5, 'days_week2' => 5],
    ],
]);
check('B: child0 ccs_pct = 0.90',            $r['children'][0]['ccs_pct'], 0.90);
check('B: child0 isHigherCCS = false',       $r['children'][0]['isHigherCCS'], false);
check('B: child1 ccs_pct = 0.95',            $r['children'][1]['ccs_pct'], 0.95);
check('B: child1 isHigherCCS = true',        $r['children'][1]['isHigherCCS'], true);
check('B: child0 fortnightSub = 648',        $r['children'][0]['fortnightSub'], 648.0);
check('B: child1 fortnightSub = 684',        $r['children'][1]['fortnightSub'], 684.0);
check('B: total fortnightSub = 1332',        $r['totals']['fortnightSub'], 1332.0);
check('B: total outPocket = 668',            $r['totals']['outPocket'], 668.0);

/* ============================================================= *
 *  6. High-activity 100h fortnight (activity > 48)
 *     income=50000, single age3, 10h/day, $100/day, 5+5, wh=0
 * ============================================================= */
$r = $engine->calculate([
    'income' => 50000, 'activity_hours' => 49, 'withholding_pct' => 0, 'is_atsi' => false,
    'as_of' => $AS_OF,
    'children' => [
        ['dob' => '2023-01-01', 'hours_per_day' => 10, 'fee_per_day' => 100, 'days_week1' => 5, 'days_week2' => 5],
    ],
]);
check('C: ccs_hours_per_fortnight = 100',    $r['ccs_hours_per_fortnight'], 100);
check('C: fortnightSub = 900',               $r['children'][0]['fortnightSub'], 900.0);
check('C: outPocket = 100',                  $r['children'][0]['outPocket'], 100.0);

/* ============================================================= *
 *  7. ATSI forces 100h even with low activity
 * ============================================================= */
$r = $engine->calculate([
    'income' => 50000, 'activity_hours' => 0, 'withholding_pct' => 0, 'is_atsi' => true,
    'as_of' => $AS_OF,
    'children' => [
        ['dob' => '2023-01-01', 'hours_per_day' => 10, 'fee_per_day' => 100, 'days_week1' => 5, 'days_week2' => 5],
    ],
]);
check('D: ATSI -> ccs_hours_per_fortnight = 100', $r['ccs_hours_per_fortnight'], 100);

/* ============================================================= *
 *  8. School-age child uses OSHC cap (12.81), not centre (14.63)
 *     age8, $90/day over 6h => hourlyFee 15 > cap 12.81
 * ============================================================= */
$r = $engine->calculate([
    'income' => 50000, 'activity_hours' => 0, 'withholding_pct' => 0, 'is_atsi' => false,
    'as_of' => $AS_OF,
    'children' => [
        ['dob' => '2018-01-01', 'hours_per_day' => 6, 'fee_per_day' => 90, 'days_week1' => 5, 'days_week2' => 5],
    ],
]);
check('E: school-age hourlyCap = 12.81',     $r['children'][0]['hourlyCap'], 12.81);
check('E: hourlyFee = 15',                   $r['children'][0]['hourlyFee'], 15.0);

/* ============================================================= *
 *  8b. Percentage round-trip parity (mirrors browser toFixed(2))
 *      income=131000 -> standard 80.8558 stored/used as 80.86
 * ============================================================= */
$r = $engine->calculate([
    'income' => 131000, 'activity_hours' => 49, 'withholding_pct' => 0.05, 'is_atsi' => false,
    'as_of' => $AS_OF,
    'children' => [
        ['dob' => '2023-01-01', 'hours_per_day' => 10, 'fee_per_day' => 100, 'days_week1' => 5, 'days_week2' => 5],
    ],
]);
check('G: standard_pct rounded to 0.8086', $r['standard_pct'], 0.8086, 0.00001);
check('G: higher_pct = 0.95',              $r['higher_pct'], 0.95, 0.00001);

/* ============================================================= *
 *  8c. Care-type hourly caps (Phase 1B)
 * ============================================================= */
function cap_for($engine, $careType, $dob, $AS_OF) {
    $r = $engine->calculate([
        'income' => 50000, 'activity_hours' => 0, 'withholding_pct' => 0, 'is_atsi' => false,
        'as_of' => $AS_OF,
        'children' => [
            ['dob' => $dob, 'care_type' => $careType, 'hours_per_day' => 10, 'fee_per_day' => 200, 'days_week1' => 5, 'days_week2' => 5],
        ],
    ]);
    return $r['children'][0]['hourlyCap'];
}
$YOUNG = '2023-01-01'; // age 3
$SCHOOL = '2018-01-01'; // age 8
check('cap: cbdc young = 14.63',  cap_for($engine, 'cbdc', $YOUNG, $AS_OF), 14.63);
check('cap: cbdc school = 12.81', cap_for($engine, 'cbdc', $SCHOOL, $AS_OF), 12.81);
check('cap: fdc young = 13.56',   cap_for($engine, 'fdc', $YOUNG, $AS_OF), 13.56);
check('cap: fdc school = 13.56',  cap_for($engine, 'fdc', $SCHOOL, $AS_OF), 13.56);
check('cap: oshc young = 14.63',  cap_for($engine, 'oshc', $YOUNG, $AS_OF), 14.63);
check('cap: oshc school = 12.81', cap_for($engine, 'oshc', $SCHOOL, $AS_OF), 12.81);
check('cap: ihc = 39.80',         cap_for($engine, 'ihc', $YOUNG, $AS_OF), 39.80);
check('cap: default(no care_type) young = 14.63', cap_for($engine, '', $YOUNG, $AS_OF), 14.63);

/* ============================================================= *
 *  9. Period scaling (renderSummary multipliers)
 * ============================================================= */
$totals = ['fortnightSub' => 100.0];
check('F: week = 50',     $engine->scale_totals($totals, 'week')['fortnightSub'], 50.0);
check('F: fortnight = 100',$engine->scale_totals($totals, 'fortnight')['fortnightSub'], 100.0);
check('F: month = 216.67', $engine->scale_totals($totals, 'month')['fortnightSub'], 100.0 * 26 / 12);
check('F: year = 2600',   $engine->scale_totals($totals, 'year')['fortnightSub'], 2600.0);

/* ============================================================= *
 *  Report
 * ============================================================= */
echo "\n";
foreach ($failures as $f) {
    echo $f . "\n";
}
echo "\n========================================\n";
echo "  CCSEngine tests: {$pass} passed, {$fail} failed\n";
echo "========================================\n";

exit($fail === 0 ? 0 : 1);
