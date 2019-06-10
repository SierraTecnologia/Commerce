{!! csrf_field() !!}

<input type="hidden" id="exp_month" data-sitecpayment="exp-month">
<input type="hidden" id="exp_year" data-sitecpayment="exp-year">

<div class="form-group number">
    <label for="number">Number</label>
    <input class="form-control" type="text" id="number" required placeholder="Card Number" value="{{ old('number') }}" data-sitecpayment="number">
</div>

<div class="form-group name">
    <label for="name">Name</label>
    <input class="form-control" type="text" id="name" required placeholder="Full Name" value="{{ auth()->user()->name }}" data-sitecpayment="name">
</div>

<div class="form-group expiry">
    <label for="expiry">Expiry Date</label>
    <input class="form-control" type="text" id="expiry" required placeholder="MM/YYYY" value="{{ old('expiry') }}">
</div>

<div class="form-group cvc">
    <label for="cvc">CVC</label>
    <input class="form-control" type="text" id="cvc" required placeholder="CVC" value="{{ old('cvc') }}" data-sitecpayment="cvc">
</div>