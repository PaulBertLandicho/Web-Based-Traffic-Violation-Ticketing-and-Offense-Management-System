<div class="col-md-6">
    <div class="mycard">
        <div class="mycard-header">
            <h3 class="mycard-heading-charts">Pending Fine and Paid Fine Amount</h3>
        </div>
        <div class="mycard-content text-center">
            <canvas id="PendingPaidfines" height="200"></canvas>
            <div class="summary mt-3">
                <strong>Summary:</strong><br>
                <ul class="text-left" style="list-style-type: disc; padding-left: 20px;">
                    <li><strong>Paid Fines:</strong> ₱{{ number_format($paidFineAmount, 2) }}<br></li>
                    <li><strong>Pending Fines:</strong> ₱{{ number_format($pendingFineAmount, 2) }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="mycard">
        <div class="mycard-header">
            <h3 class="mycard-heading-charts">Total Issued Driver Count and Traffic Enforcer Count</h3>
        </div>
        <div class="mycard-content text-center">
            <canvas id="DriverAndEnforcersCount" height="200"></canvas>
            <div class="summary mt-3">
                <strong>Summary:</strong><br>
                <ul class="text-left" style="list-style-type: disc; padding-left: 20px;">
                    <li><strong>Total Issued Drivers:</strong> {{ $issuedDriversCount }}<br></li>
                    <li><strong>Total Registered Enforcers:</strong> {{ $enforcersCount }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
<div class="row p-2">
    <div class="col-md-6">
        <div class="mycard">
            <div class="mycard-header">
                <h3 class="mycard-heading-charts">Vehicle Classification</h3>
            </div>
            <div class="mycard-content text-center">
                <canvas id="vehicleClassChart" height="200"></canvas>

                <div class="summary mt-3" id="vehicle-summary">
                    <strong>Summary:</strong><br>
                    <ul class="text-left" style="list-style-type: disc; padding-left: 20px;">
                        @foreach($vehicleTypesData as $vehicle)
                        <li><strong>{{ $vehicle->vehicle_type }}:</strong>{{ $vehicle->count }} vehicles</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mycard">
            <div class="mycard-header">
                <h3 class="mycard-heading-charts">
                    Violation Type
                </h3>
            </div>
            <div class="mycard-content text-center">
                <canvas id="violationTypeChart" height="200"></canvas>

                <div class="summary mt-3" id="violation-summary">
                    <strong>Summary (Top 6 Violations):</strong><br>
                    <ul class="text-left" style="list-style-type: disc; padding-left: 20px;">
                        @foreach($violationTypeData as $violation => $count)
                        <li><strong>{{ ucwords($violation) }}:</strong> {{ $count }}</li>
                        @endforeach

                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="row p-2">
    <div class="col-md-12">
        <div class="mycard">
            <div class="mycard-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="mycard-heading-charts mb-2 mb-md-0">
                    Number of Issued Fine
                </h3>
                <div class="d-flex gap-2 align-items-center">
                    <select id="issuedFineMonth" class="form-control form-control-sm mr-2">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                    </select>

                    <select id="issuedFineYear" class="form-control form-control-sm">
                        @for ($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>

                </div>
            </div>
            <div class="mycard-content">
                <canvas id="issuedFineCount" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row p-2">
    <div class="col-md-12">
        <div class="mycard">
            <div class="mycard-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="mycard-heading-charts mb-2 mb-md-0">
                    Total Fine Amount
                </h3>
                <div class="d-flex gap-2 align-items-center">
                    <select id="totalAmountMonth" class="form-control form-control-sm mr-2">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                    </select>

                    <select id="totalAmountYear" class="form-control form-control-sm">
                        @for ($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>

                </div>
            </div>
            <div class="mycard-content">
                <canvas id="totalFineAmount" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row p-2">
    <div class="col-md-12">
        <div class="mycard">
            <div class="mycard-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="mycard-heading-charts mb-2 mb-md-0">
                    Number of Violations per Barangay
                </h3>

                <div class="d-flex gap-2 align-items-center">
                    <select id="filterMonth" class="form-control form-control-sm mr-2">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                    </select>

                    <select id="filterYear" class="form-control form-control-sm">
                        @for ($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>

                </div>
            </div>
            <div class="mycard-content">
                <canvas id="violationsPerBarangayChart" height="350"></canvas>
            </div>
        </div>
    </div>
</div>