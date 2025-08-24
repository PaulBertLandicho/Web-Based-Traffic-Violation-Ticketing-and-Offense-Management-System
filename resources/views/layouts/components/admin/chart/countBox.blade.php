@php
$stats = [
['label' => 'Total Fine Amount (₱)', 'value' => number_format($totalFineAmount, 2), 'data-placement' => 'bottom', 'data-title' => 'Reported total fine amount', 'icon' => 'fas fa-coins', 'bg' => 'bg-secondary'],
['label' => 'Pending Fine Amount (₱)', 'value' => number_format($pendingFineAmount, 2), 'data-placement' => 'bottom', 'data-title' => 'Total amount of pending fine', 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-info'],
['label' => 'Paid Fine Amount (₱)', 'value' => number_format($paidFineAmount, 2), 'data-placement' => 'bottom', 'data-title' => 'Total amount of paid fine', 'icon' => 'fas fa-coins', 'bg' => 'bg-warning'],
['label' => 'Provisions Count', 'value' => $provisionsCount, 'data-placement' => 'bottom', 'data-title' => 'Total number of traffic provisions', 'icon' => 'fas fa-receipt', 'bg' => 'bg-primary'],
['label' => 'Issued Drivers Count', 'value' => $issuedDriversCount, 'data-placement' => 'bottom', 'data-title' => 'Drivers issued with violations', 'icon' => 'fas fa-users', 'bg' => 'bg-danger'],
['label' => 'Traffic Enforcer Count', 'value' => $enforcersCount, 'data-placement' => 'bottom', 'data-title' => 'Registered traffic enforcers', 'icon' => 'fas fa-users-cog', 'bg' => 'bg-dark'],
];
@endphp


@foreach ($stats as $stat)

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
                <h4 class="modern-value counter">{{ $stat['value'] }}</h4>
            </div>
        </div>
    </div>
</div>
<!--Second count box end-->

@endforeach