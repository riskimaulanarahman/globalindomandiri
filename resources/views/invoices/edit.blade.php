@extends('layouts.app')
@section('title','Edit Invoice')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 text-gray-800">Edit Invoice</h1>
  <div class="d-flex align-items-center">
    <x-help-button id="help-invoices-edit" class="mr-2" title="Bantuan • Edit Invoice">
      <ul>
        <li><strong>Header</strong>: Date, Due Date/TOP, Customer, Terms, Remarks → klik Save.</li>
        <li><strong>Tambah item</strong>:
          <br>- <em>Manual line</em>: isi Description, Qty, Amount.
          <br>- <em>Dari Shipment</em>: pilih resi untuk otomatis isi Description.</li>
        <li><strong>Status</strong>: Draft → Sent → Partially Paid/Paid/Overdue (otomatis mengikuti pembayaran).</li>
        <li><strong>Pembayaran</strong>: gunakan tombol <em>Add Payment</em>; jumlah ≤ outstanding.</li>
        <li><strong>Print</strong>: gunakan tombol Print untuk cetak/arsip PDF dari browser.</li>
      </ul>
    </x-help-button>
    <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-success mr-2"><i class="fas fa-money-bill mr-1"></i> Add Payment</a>
    @if($invoice->status === 'Draft')
      <form action="{{ route('invoices.markSent', $invoice) }}" method="POST" class="mr-2" data-confirm="Yakin ingin menandai invoice ini sebagai 'Sent'?">
        @csrf
        <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane mr-1"></i> Mark as Sent</button>
      </form>
    @endif
    <form action="{{ route('invoices.refresh', $invoice) }}" method="POST" class="mr-2" data-confirm="Refresh all amounts from related shipments?">
      @csrf
      <button type="submit" class="btn btn-outline-info"><i class="fas fa-sync mr-1"></i> Refresh from Shipments</button>
    </form>
    <a href="{{ route('invoices.print',$invoice) }}" target="_blank" class="btn btn-outline-secondary mr-2"><i class="fas fa-print mr-1"></i> Print</a>
    @if($invoice->status === 'Draft' && !$invoice->payments()->exists())
      <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="mr-2" data-confirm="Hapus invoice ini?">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash mr-1"></i> Delete</button>
      </form>
    @endif
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

@if (session('status'))
  <div class="alert alert-success">{{ ucfirst(str_replace('-',' ',session('status'))) }}.</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card shadow mb-4"><div class="card-body">
  @include('invoices._header_form')
</div></div>

<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Lines</h6>
    <form action="{{ route('invoices.lines.add',$invoice) }}" method="post" class="form-inline">
      @csrf
      <div class="form-group mr-2">
        <select name="shipment_id" class="form-control" title="Shipment">
          <option value="">Manual line...</option>
          @foreach($eligibleShipments as $s)
            <option value="{{ $s->id }}">{{ $s->resi_no }} | {{ $s->origin?->city }}→{{ $s->destination?->city }} | {{ number_format((float)$s->total_cost,2) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group mr-2">
        <input type="text" name="description" class="form-control" placeholder="Description (optional)">
      </div>
      <div class="form-group mr-2">
        <input type="number" step="0.01" min="0.01" name="qty" class="form-control" value="1" placeholder="Qty" required>
      </div>
      <div class="form-group mr-2">
        <input type="text" name="uom" class="form-control" value="Trip" placeholder="UOM">
      </div>
      <div class="form-group mr-2">
        <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="Amount" required>
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus mr-1"></i> Add Line</button>
    </form>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Description</th>
            <th>Shipment</th>
            <th class="text-right">Qty</th>
            <th class="text-right">UOM</th>
            <th class="text-right">Price</th>
            <th class="text-right">Line Total</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoice->lines as $line)
            <tr>
              <td>{{ $line->description }}</td>
              <td>{{ $line->shipment?->resi_no }}</td>
              <td class="text-right">{{ number_format((float)$line->qty,2) }}</td>
              <td class="text-right">{{ $line->uom ?? 'Trip' }}</td>
              <td class="text-right">{{ number_format((float)$line->amount,2) }}</td>
              <td class="text-right">{{ number_format((float)$line->qty * (float)$line->amount,2) }}</td>
              <td>
                <form action="{{ route('invoices.lines.remove', [$invoice,$line]) }}" method="POST" data-confirm="Hapus baris ini?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No lines</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
