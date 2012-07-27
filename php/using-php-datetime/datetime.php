<?php
// Comparison and Formatting
$start 	= new DateTime('now');
$end 	= new DateTime('2014-02-01');

echo 'Start: ' . $start->format('d M Y') . "\n";
echo 'End: ' . $end->format('d M Y') . "\n";

$diff = $end->diff($start);
echo 'Comparison: ' . ($start < $end ? 'Start is less than End' : 'End is less than Start') . "\n";
echo 'Interval: ' . $diff->format('%y Year(s), %m Month(s), %d Day(s)') . "\n\n";


// Intervals and Formatting
$start    = new DateTime('now');
$end 	  = new DateTime('next friday');
$interval = new DateInterval('P1D');

echo 'Start: ' . $start->format('d M Y') . "\n";
echo 'End: ' . $end->format('d M Y') . "\n";
echo 'Days Between: ' . "\n";

$period	= new DatePeriod($start, $interval, $end);
foreach ($period as $date) {
    echo $date->format('d M Y') . "\n";
}


// Format Time Ago Helper
function formatTimeAgo($timestamp)
{
    $then = new DateTime($timestamp);
    $now = new DateTime('now');
    $interval = $now->diff($then);
	
    $bits = array(
        'year'    => $interval->y,
        'month'   => $interval->m,
        'day' 	  => $interval->d,
        'hour'    => $interval->h,
        'minute'  => $interval->i,
        'second'  => $interval->s,
    );
	
    foreach ($bits as $type => $value) {
        if ($value > 0) {
            return $value . ' ' . ($value > 1 ? $type . 's' : $type) . ' ago';
        }
    }

    return false;
}

echo 'Format Time Ago: ' . formatTimeAgo('2012-08-12') . "\n";


// Usage Example:
// Get the next twelve months and display them.
$start = new DateTime;
$start->modify('first day of this month');

$end = clone $start;
$end->modify('+12 months');

$interval = new DateInterval('P1M');
$period = new DatePeriod($start, $interval, $end);

$months = array();
foreach ($period as $date) {
    $months[] = $date->format('F');
}

$years = array($start->format('Y'), $end->format('Y'));
$years = array_unique($years);

echo 'Years: ' . implode('/', $years) . "\n";
echo 'Months: ' . implode(', ', $months) . "\n";


// Usage Example:
// Get all the days in a month.
$yearMonth = '2013-06';

$start = new DateTime($yearMonth);
$start->modify('first day of this month');

// If the time isn't set then the DatePeriod will be missing the last day of the month.
$end = new DateTime($yearMonth);
$end->modify('last day of this month');
$end->modify('+23 hours 59 minutes 59 seconds');

/*
// Alternatively, get the month including out-of-month days from Sunday to Sunday.
$start = new DateTime($year_month);
$start->modify('first day of this month');
$start = ($start->format('w') != 0 ? $start->modify('previous sunday') : $start);

$end = new DateTime($year_month);
$end->modify('last day of this month');
$end = ($end->format('w') != 6 ? $end->modify('next saturday') : $end);
$end->modify('+23 hours 59 minutes 59 seconds');
*/

$interval = new DateInterval('P1D');
$days = new DatePeriod($start, $interval, $end);

foreach ($days as $day) {
    echo 'Day: ' . $day->format('Y-m-d') . "\n";
}