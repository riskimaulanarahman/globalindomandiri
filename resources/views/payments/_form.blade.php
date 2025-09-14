@php($isEdit = $payment && $payment->exists)

<form method="POST" action="{{ $isEdit ? route('payments.update',$payment) : route('payments.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-5">
      <label for="invoice_id">Invoice</label>
      <select id="invoice_id" name="invoice_id" class="form-control select2 @error('invoice_id') is-invalid @enderror" required>
        <option value="">-- Select Invoice --</option>
        @foreach($invoices as $i)
          <option value="{{ $i->id }}" {{ (int)old('invoice_id',$payment->invoice_id) === $i->id ? 'selected' : '' }}>
            {{ $i->invoice_no }} | {{ $i->customer?->name }} | Out: {{ number_format((float)$i->outstanding,2) }}
          </option>
        @endforeach
      </select>
      @error('invoice_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="paid_amount">Paid Amount</label>
      <input type="number" step="0.01" min="0.01" id="paid_amount" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount',$payment->paid_amount) }}" required>
      @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="paid_date">Paid Date</label>
      <input type="date" id="paid_date" name="paid_date" class="form-control @error('paid_date') is-invalid @enderror" value="{{ old('paid_date',$payment->paid_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
      @error('paid_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="method">Method</label>
      <select id="method" name="method" class="form-control select2 @error('method') is-invalid @enderror" required>
        @foreach($methods as $m)
          <option value="{{ $m }}" {{ old('method',$payment->method) === $m ? 'selected' : '' }}>{{ $m }}</option>
        @endforeach
      </select>
      @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="ref_no">Reference</label>
      <input type="text" id="ref_no" name="ref_no" class="form-control @error('ref_no') is-invalid @enderror" value="{{ old('ref_no',$payment->ref_no) }}" placeholder="Bank ref, notes, etc">
      @error('ref_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>
