@extends('layouts.layout')

@section('content')
<div class="hero_area">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">

        <div class="card shadow-lg p-4" style="width: 420px; border-radius: 15px;">

            <h4 class="text-center mb-3 font-weight-bold">Email Verification</h4>
            <p class="text-center text-muted">Enter the 6-digit OTP sent to your email.</p>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('enforcer.verify-otp') }}" method="POST" id="otpForm">
                @csrf

                <div class="d-flex justify-content-between mb-4">
                    <input type="text" maxlength="1" class="otp-input form-control text-center" />
                    <input type="text" maxlength="1" class="otp-input form-control text-center" />
                    <input type="text" maxlength="1" class="otp-input form-control text-center" />
                    <input type="text" maxlength="1" class="otp-input form-control text-center" />
                    <input type="text" maxlength="1" class="otp-input form-control text-center" />
                    <input type="text" maxlength="1" class="otp-input form-control text-center" />
                </div>

                <input type="hidden" name="otp" id="otp_final">

                <button class="btn btn-primary btn-block py-2">Verify OTP</button>
            </form>

            <div class="text-center mt-3">
                <p class="mb-1">Didn't receive the code?</p>

                <button id="resendBtn" class="btn btn-link p-0" disabled>
                    Resend OTP <span id="timer">(05:00)</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .otp-input {
        width: 55px;
        height: 60px;
        font-size: 22px;
        border: 2px solid #ced4da;
        border-radius: 10px;
        margin-right: 5px;
        box-shadow: none !important;
        transition: 0.2s;
    }

    .otp-input:last-child {
        margin-right: 0;
    }

    .otp-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 6px rgba(0, 123, 255, 0.5) !important;
    }
</style>

<script>
    // Handle OTP input fields
    const inputs = document.querySelectorAll('.otp-input');

    inputs.forEach((input, index) => {
        input.addEventListener("input", () => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateOtp();
        });

        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    function updateOtp() {
        let otp = "";
        inputs.forEach(input => otp += input.value);
        document.getElementById("otp_final").value = otp;
    }

    // Countdown timer for resend OTP
    let timeLeft = 300;
    const timerLabel = document.getElementById("timer");
    const resendBtn = document.getElementById("resendBtn");

    const countdown = setInterval(() => {
        let m = String(Math.floor(timeLeft / 60)).padStart(2, '0');
        let s = String(timeLeft % 60).padStart(2, '0');
        timerLabel.textContent = `(${m}:${s})`;

        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerLabel.textContent = "";
            resendBtn.disabled = false;
            resendBtn.style.opacity = "1";
        }
        timeLeft--;
    }, 1000);

    // AJAX resend OTP
    resendBtn.addEventListener("click", () => {
        resendBtn.disabled = true;
        resendBtn.textContent = "Sending...";

        fetch("{{ route('enforcer.resend-otp') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                resendBtn.textContent = "OTP Sent!";
                setTimeout(() => location.reload(), 1200);
            });
    });
</script>

@endsection