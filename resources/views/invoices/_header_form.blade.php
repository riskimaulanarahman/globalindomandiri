@php($isEdit = $invoice && $invoice->exists)

<form method="POST" action="{{ $isEdit ? route('invoices.update',$invoice) : route('invoices.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-3">
      <label>Invoice No</label>
      <input type="text" class="form-control" value="{{ $invoice->invoice_no ?? 'Auto' }}" disabled>
    </div>
    <div class="form-group col-md-3">
      <label for="po_no">PO No (optional)</label>
      <input type="text" id="po_no" name="po_no" class="form-control @error('po_no') is-invalid @enderror" value="{{ old('po_no',$invoice->po_no) }}" placeholder="PO number">
      @error('po_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="invoice_date">Invoice Date</label>
      <input type="date" id="invoice_date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date',$invoice->invoice_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
      @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="due_date">Due Date</label>
      <input type="date" id="due_date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date',$invoice->due_date?->format('Y-m-d')) }}">
      @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="top_days">TOP (days)</label>
      <input type="number" min="0" max="365" id="top_days" name="top_days" class="form-control @error('top_days') is-invalid @enderror" value="{{ old('top_days',$invoice->top_days) }}" placeholder="Optional">
      @error('top_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="customer_id">Customer</label>
      <select id="customer_id" name="customer_id" class="form-control select2 @error('customer_id') is-invalid @enderror" required>
        <option value="">-- Select Customer --</option>
        @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ (int)old('customer_id',$invoice->customer_id) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
      </select>
      @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-6">
      <label for="terms_text">Terms</label>
      <input type="text" id="terms_text" name="terms_text" class="form-control @error('terms_text') is-invalid @enderror" value="{{ old('terms_text',$invoice->terms_text) }}">
      @error('terms_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-12">
      <label for="remarks">Remarks</label>
      <input type="text" id="remarks" name="remarks" class="form-control @error('remarks') is-invalid @enderror" value="{{ old('remarks',$invoice->remarks) }}">
      @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  @if($isEdit)
    <div class="form-row">
      <div class="form-group col-md-3">
        <label for="status">Status</label>
        <select id="status" name="status" class="form-control select2 @error('status') is-invalid @enderror">
          @foreach(($statuses ?? []) as $s)
            <option value="{{ $s }}" {{ old('status',$invoice->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="form-group col-md-3">
        <label>Total</label>
        <input type="text" class="form-control" value="{{ number_format((float)$invoice->total_amount,2) }}" disabled>
      </div>
      <div class="form-group col-md-3">
        <label>Outstanding</label>
        <input type="text" class="form-control" value="{{ number_format((float)$invoice->outstanding,2) }}" disabled>
      </div>
    </div>
  @endif

  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back</a>
  </div>
</form>
