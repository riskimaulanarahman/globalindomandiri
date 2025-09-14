@php($isEdit = $shipment && $shipment->exists)

<form method="POST" action="{{ $isEdit ? route('shipments.update',$shipment) : route('shipments.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="resi_no">Resi No</label>
      <input type="text" id="resi_no" name="resi_no" class="form-control @error('resi_no') is-invalid @enderror" value="{{ old('resi_no',$shipment->resi_no) }}">
      @error('resi_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-5">
      <label for="customer_id">Billing Customer</label>
      <select id="customer_id" name="customer_id" class="form-control select2 @error('customer_id') is-invalid @enderror" required>
        <option value="">-- Select Customer --</option>
        @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ (int)old('customer_id',$shipment->customer_id) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
      </select>
      @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="service_type">Service Type</label>
      <select id="service_type" name="service_type" class="form-control select2 @error('service_type') is-invalid @enderror" required>
        <option value="">-- Select Service --</option>
        @foreach(($services ?? []) as $svc)
          <option value="{{ $svc->name }}" {{ old('service_type',$shipment->service_type) === $svc->name ? 'selected' : '' }}>
            {{ $svc->name }}@if($svc->code) ({{ $svc->code }}) @endif
          </option>
        @endforeach
      </select>
      @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="sender_customer_id">Customer (Sender)</label>
      <select id="sender_customer_id" name="sender_customer_id" class="form-control select2 @error('sender_customer_id') is-invalid @enderror">
        <option value="">-- Select Customer (Sender) --</option>
        @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ (int)old('sender_customer_id',$shipment->sender_customer_id) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
      </select>
      @error('sender_customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <div class="mt-2">
        <label for="sender_contact_id">PIC (Sender)</label>
        <select id="sender_contact_id" name="sender_contact_id" class="form-control select2" data-placeholder="-- Select PIC --" data-selected="{{ old('sender_contact_id', $shipment->sender_contact_id) }}">
          <option value="">-- Select PIC --</option>
        </select>
      </div>
    </div>
    <div class="form-group col-md-6">
      <label for="receiver_customer_id">Customer (Receiver)</label>
      <select id="receiver_customer_id" name="receiver_customer_id" class="form-control select2 @error('receiver_customer_id') is-invalid @enderror">
        <option value="">-- Select Customer (Receiver) --</option>
        @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ (int)old('receiver_customer_id',$shipment->receiver_customer_id) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
      </select>
      @error('receiver_customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <div class="mt-2">
        <label for="receiver_contact_id">PIC (Receiver)</label>
        <select id="receiver_contact_id" name="receiver_contact_id" class="form-control select2" data-placeholder="-- Select PIC --" data-selected="{{ old('receiver_contact_id', $shipment->receiver_contact_id) }}">
          <option value="">-- Select PIC --</option>
        </select>
      </div>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-4">
      <label for="origin_id">Origin</label>
      <select id="origin_id" name="origin_id" class="form-control select2 @error('origin_id') is-invalid @enderror" required>
        <option value="">-- Select Origin --</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}" {{ (int)old('origin_id',$shipment->origin_id) === $loc->id ? 'selected' : '' }}>
            {{ $loc->city }}{{ $loc->province ? ', '.$loc->province : '' }}{{ $loc->country ? ' - '.$loc->country : '' }}
          </option>
        @endforeach
      </select>
      @error('origin_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="destination_id">Destination</label>
      <select id="destination_id" name="destination_id" class="form-control select2 @error('destination_id') is-invalid @enderror" required>
        <option value="">-- Select Destination --</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}" {{ (int)old('destination_id',$shipment->destination_id) === $loc->id ? 'selected' : '' }}>
            {{ $loc->city }}{{ $loc->province ? ', '.$loc->province : '' }}{{ $loc->country ? ' - '.$loc->country : '' }}
          </option>
        @endforeach
      </select>
      @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
      <label for="rate_id">Rate (optional)</label>
      <select id="rate_id" name="rate_id" class="form-control select2 @error('rate_id') is-invalid @enderror">
        <option value="">-- Select Rate --</option>
        @foreach($rates as $r)
          <option value="{{ $r->id }}" {{ (int)old('rate_id',$shipment->rate_id) === $r->id ? 'selected' : '' }}>
            {{ $r->origin?->city }} → {{ $r->destination?->city }} | {{ $r->service_type }} | {{ number_format((float)$r->price,2) }}
          </option>
        @endforeach
      </select>
      @error('rate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="weight_actual">Weight (kg)</label>
      <input type="number" step="0.01" min="0" id="weight_actual" name="weight_actual" class="form-control @error('weight_actual') is-invalid @enderror" value="{{ old('weight_actual',$shipment->weight_actual) }}">
      @error('weight_actual')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="volume_weight">Volume Weight (kg)</label>
      <input type="number" step="0.01" min="0" id="volume_weight" name="volume_weight" class="form-control @error('volume_weight') is-invalid @enderror" value="{{ old('volume_weight',$shipment->volume_weight) }}">
      @error('volume_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="koli_count">Koli</label>
      <input type="number" min="0" id="koli_count" name="koli_count" class="form-control @error('koli_count') is-invalid @enderror" value="{{ old('koli_count',$shipment->koli_count) }}">
      @error('koli_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="status">Status</label>
      <select id="status" name="status" class="form-control select2 @error('status') is-invalid @enderror" required>
        @foreach($statuses as $s)
          <option value="{{ $s }}" {{ old('status',$shipment->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
      </select>
      @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="sender_name">Sender</label>
      <input readonly type="text" id="sender_name" name="sender_name" class="form-control @error('sender_name') is-invalid @enderror" value="{{ old('sender_name',$shipment->sender_name) }}">
      @error('sender_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <textarea readonly name="sender_address" id="sender_address" class="form-control mt-2 @error('sender_address') is-invalid @enderror" rows="2" placeholder="Sender address">{{ old('sender_address',$shipment->sender_address) }}</textarea>
      @error('sender_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <div class="form-row mt-2">
        <div class="col">
          <input readonly type="text" name="sender_pic" id="sender_pic" class="form-control @error('sender_pic') is-invalid @enderror" placeholder="Sender PIC" value="{{ old('sender_pic',$shipment->sender_pic) }}">
          @error('sender_pic')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col">
          <input readonly type="text" name="sender_phone" id="sender_phone" class="form-control @error('sender_phone') is-invalid @enderror" placeholder="Sender Phone" value="{{ old('sender_phone',$shipment->sender_phone) }}">
          @error('sender_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
    <div class="form-group col-md-6">
      <label for="receiver_name">Receiver</label>
      <input readonly type="text" id="receiver_name" name="receiver_name" class="form-control @error('receiver_name') is-invalid @enderror" value="{{ old('receiver_name',$shipment->receiver_name) }}">
      @error('receiver_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <textarea readonly name="receiver_address" id="receiver_address" class="form-control mt-2 @error('receiver_address') is-invalid @enderror" rows="2" placeholder="Receiver address">{{ old('receiver_address',$shipment->receiver_address) }}</textarea>
      @error('receiver_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <div class="form-row mt-2">
        <div class="col">
          <input readonly type="text" name="receiver_pic" id="receiver_pic" class="form-control @error('receiver_pic') is-invalid @enderror" placeholder="Receiver PIC" value="{{ old('receiver_pic',$shipment->receiver_pic) }}">
          @error('receiver_pic')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col">
          <input readonly type="text" name="receiver_phone" id="receiver_phone" class="form-control @error('receiver_phone') is-invalid @enderror" placeholder="Receiver Phone" value="{{ old('receiver_phone',$shipment->receiver_phone) }}">
          @error('receiver_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="item_desc">Item Description (Isi Barang)</label>
      <input type="text" id="item_desc" name="item_desc" class="form-control @error('item_desc') is-invalid @enderror" value="{{ old('item_desc',$shipment->item_desc) }}">
      @error('item_desc')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-6">
      <label for="notes">Notes (Keterangan)</label>
      <input type="text" id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes',$shipment->notes) }}">
      @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="departed_at">Departed At</label>
      <input type="datetime-local" id="departed_at" name="departed_at" class="form-control @error('departed_at') is-invalid @enderror" value="{{ old('departed_at', optional($shipment->departed_at)->format('Y-m-d\TH:i')) }}">
      @error('departed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="received_at">Received At</label>
      <input type="datetime-local" id="received_at" name="received_at" class="form-control @error('received_at') is-invalid @enderror" value="{{ old('received_at', optional($shipment->received_at)->format('Y-m-d\TH:i')) }}">
      @error('received_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="base_fare">Base Fare</label>
      <input type="number" step="0.01" min="0" id="base_fare" name="base_fare" class="form-control @error('base_fare') is-invalid @enderror" value="{{ old('base_fare',$shipment->base_fare) }}" placeholder="Auto if rate selected">
      @error('base_fare')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="packing_fee">Packing</label>
      <input type="number" step="0.01" min="0" id="packing_fee" name="packing_fee" class="form-control @error('packing_fee') is-invalid @enderror" value="{{ old('packing_fee',$shipment->packing_fee) }}">
      @error('packing_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="insurance_fee">Insurance</label>
      <input type="number" step="0.01" min="0" id="insurance_fee" name="insurance_fee" class="form-control @error('insurance_fee') is-invalid @enderror" value="{{ old('insurance_fee',$shipment->insurance_fee) }}">
      @error('insurance_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="other_fee">Other</label>
      <input type="number" step="0.01" min="0" id="other_fee" name="other_fee" class="form-control @error('other_fee') is-invalid @enderror" value="{{ old('other_fee',$shipment->other_fee) }}">
      @error('other_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="discount">Discount</label>
      <input type="number" step="0.01" min="0" id="discount" name="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount',$shipment->discount) }}">
      @error('discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="ppn">PPN</label>
      <input type="number" step="0.01" min="0" id="ppn" name="ppn" class="form-control @error('ppn') is-invalid @enderror" value="{{ old('ppn',$shipment->ppn) }}">
      @error('ppn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-2">
      <label for="pph23">PPh23</label>
      <input type="number" step="0.01" min="0" id="pph23" name="pph23" class="form-control @error('pph23') is-invalid @enderror" value="{{ old('pph23',$shipment->pph23) }}">
      @error('pph23')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('shipments.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>


@push('scripts')
<script>
  $(function(){
    const $origin = $('#origin_id');
    const $destination = $('#destination_id');
    const $service = $('#service_type');
    const $rate = $('#rate_id');
    const $wAct = $('#weight_actual');
    const $wVol = $('#volume_weight');
    const $baseFare = $('#base_fare');
    const $customer = $('#customer_id'); // billing (no autofill)
    const $senderCustomer = $('#sender_customer_id');
    const $receiverCustomer = $('#receiver_customer_id');
    const $senderContact = $('#sender_contact_id');
    const $receiverContact = $('#receiver_contact_id');

    const $senderName = $('#sender_name');
    const $senderAddr = $('#sender_address');
    const $senderPhone = $('#sender_phone');
    const $senderPic = $('#sender_pic');

    const $receiverName = $('#receiver_name');
    const $receiverAddr = $('#receiver_address');
    const $receiverPhone = $('#receiver_phone');
    const $receiverPic = $('#receiver_pic');

    const ratesUrl = "{{ route('rates.options') }}";

    function chgWeight(){
      const a = parseFloat($wAct.val() || '0');
      const v = parseFloat($wVol.val() || '0');
      return Math.max(a, v);
    }

    function tryCalcBaseFare(){
      const opt = $rate[0].options[$rate[0].selectedIndex];
      if (!opt || !opt.value) return;
      const text = opt.text || '';
      const priceMatch = text.match(/\|\s([0-9\.,]+)\s*$/);
      let price = 0;
      if (priceMatch) {
        price = parseFloat(priceMatch[1].replace(/\./g,'').replace(',', '.'));
      } else if (opt.dataset && opt.dataset.price) {
        price = parseFloat(opt.dataset.price);
      }
      if (!isFinite(price)) price = 0;
      const chg = chgWeight();
      if (price > 0 && chg > 0 && (!$baseFare.val() || parseFloat($baseFare.val()) === 0)) {
        $baseFare.val((price * chg).toFixed(2));
      }
    }

    function reloadRates(){
      const params = { origin_id: $origin.val(), destination_id: $destination.val(), service_type: $service.val() };
      $.getJSON(ratesUrl, params, function(res){
        const items = (res && res.items) ? res.items : [];
        const selected = $rate.val();
        $rate.empty().append($('<option/>', { value: '', text: '-- Select Rate --' }));
        items.forEach(function(it){
          const opt = $('<option/>', { value: it.id, text: it.text });
          opt.attr('data-price', it.price);
          $rate.append(opt);
        });
        $rate.val(selected).trigger('change.select2');
      });
    }

    function applySenderCustomer(){
      const opt = $senderCustomer[0].options[$senderCustomer[0].selectedIndex];
      if (!opt) return;
      const name = opt.text || '';
      if (name) $senderName.val(name);
    }

    function applyReceiverCustomer(){
      const opt = $receiverCustomer[0].options[$receiverCustomer[0].selectedIndex];
      if (!opt) return;
      const name = opt.text || '';
      if (name) $receiverName.val(name);
    }

    function applySenderContact(){
      const opt = $senderContact[0].options[$senderContact[0].selectedIndex];
      if (!opt) return;
      const name = opt.dataset.name || '';
      const phone = opt.dataset.phone || '';
      const address = opt.dataset.address || '';
      if (name) $senderPic.val(name);
      if (phone) $senderPhone.val(phone);
      if (address) $senderAddr.val(address);
    }

    function applyReceiverContact(){
      const opt = $receiverContact[0].options[$receiverContact[0].selectedIndex];
      if (!opt) return;
      const name = opt.dataset.name || '';
      const phone = opt.dataset.phone || '';
      const address = opt.dataset.address || '';
      if (name) $receiverPic.val(name);
      if (phone) $receiverPhone.val(phone);
      if (address) $receiverAddr.val(address);
    }

    function loadContactsFor($custSel, $targetSel, applyFn){
      const custId = $custSel.val();
      const preferred = String($targetSel.data('selected') || '');
      $targetSel.empty().append($('<option/>',{value:'',text:'-- Select PIC --'}));
      if (!custId) return;
      $.getJSON(`{{ url('customers') }}/${custId}/contacts`, function(res){
        const items = (res && res.items) ? res.items : [];
        let selectedDefault = '';
        let hasPreferred = false;
        items.forEach(function(it){
          const opt = $('<option/>',{value:it.id, text: it.name});
          opt.attr('data-name', it.name || '');
          if (it.phone) opt.attr('data-phone', it.phone);
          if (it.address) opt.attr('data-address', it.address);
          if (!selectedDefault && it.is_default) selectedDefault = it.id;
          if (!hasPreferred && preferred && String(it.id) === preferred) hasPreferred = true;
          $targetSel.append(opt);
        });
        if (hasPreferred) {
          $targetSel.val(String(preferred));
        } else if (selectedDefault) {
          $targetSel.val(String(selectedDefault));
        }
        if (typeof applyFn === 'function') applyFn();
      });
    }

    $origin.on('change', reloadRates);
    $destination.on('change', reloadRates);
    $service.on('change', reloadRates);
    $rate.on('change', tryCalcBaseFare);
    $wAct.on('input', tryCalcBaseFare);
    $wVol.on('input', tryCalcBaseFare);

    $senderCustomer.on('change', function(){
      applySenderCustomer();
      $senderContact.data('selected','');
      loadContactsFor($senderCustomer, $senderContact, applySenderContact);
    });
    $receiverCustomer.on('change', function(){
      applyReceiverCustomer();
      $receiverContact.data('selected','');
      loadContactsFor($receiverCustomer, $receiverContact, applyReceiverContact);
    });
    $senderContact.on('change', applySenderContact);
    $receiverContact.on('change', applyReceiverContact);

    // Prefill on page load (useful for edit)
    applySenderCustomer();
    loadContactsFor($senderCustomer, $senderContact, applySenderContact);
    applyReceiverCustomer();
    loadContactsFor($receiverCustomer, $receiverContact, applyReceiverContact);
  });
</script>
@endpush
