@php
$stats = [
['label' => 'Enforcer Name', 'value' => $enforcerName, 'data-placement' => 'bottom', 'data-title' => 'Your Name', 'icon' => 'fas fa-user-tie', 'bg' => 'bg-warning'],
['label' => 'Reported Fine Amount (₱)', 'value' => number_format($fineAmount, 2), 'data-placement' => 'bottom', 'data-title' => 'Reported fine amount by you', 'icon' => 'fas fa-coins', 'bg' => 'bg-secondary'],
['label' => 'Assigned Area', 'value' => $assignedArea, 'data-placement' => 'bottom', 'data-title' => 'Your Assigned Area', 'icon' => 'fas fa-road', 'bg' => 'bg-info'],
['label' => 'Reported Fine Count', 'value' => $fineCount, 'data-placement' => 'bottom', 'data-title' => 'Reported fine count by you', 'icon' => 'fas fa-flag-checkered', 'bg' => 'bg-primary'],
];
@endphp


@foreach ($stats as $stat)
@php
// Remove commas, then check if it's numeric
$rawValue = str_replace(',', '', strip_tags($stat['value']));
$isNumeric = is_numeric($rawValue);
@endphp

<!--Second count box start-->
<div class="col-xl-4 col-lg-6 col-md-6">
    <div class="modern-card">
        <div data-toggle="tooltip"
            data-placement="{{ $stat['data-placement'] }}"
            title="{{ $stat['data-title'] }}"
            class="d-flex align-items-center">
            <div class="modern-icon {{ $stat['bg'] }}">
                <i class="{{ $stat['icon'] }}"></i>
            </div>
            <div class="ms-3">
                <p class="modern-label">{{ $stat['label'] }}</p>
                <h4 class="modern-value {{ $isNumeric ? 'counter' : '' }}">{{ $stat['value'] }}</h4>
            </div>
        </div>
    </div>
</div>
<!--Second count box end-->

@endforeach