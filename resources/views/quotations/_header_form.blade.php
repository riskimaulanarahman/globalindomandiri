@php($isEdit = $quotation && $quotation->exists)
@php($locked = $isEdit && in_array($quotation->status, ['Rejected','Expired','Converted','Closed']))

<form method="POST" action="{{ $isEdit ? route('quotations.update',$quotation) : route('quotations.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-3">
      <label>No</label>
      <input type="text" class="form-control" value="{{ $quotation->quote_no ?? 'Auto' }}" disabled>
    </div>
    <div class="form-group col-md-3">
      <label for="quote_date">Date</label>
      <input type="date" id="quote_date" name="quote_date" class="form-control @error('quote_date') is-invalid @enderror" value="{{ old('quote_date',$quotation->quote_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
      @error('quote_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="valid_until">Valid Until</label>
      <input type="date" id="valid_until" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until',$quotation->valid_until?->format('Y-m-d')) }}">
      @error('valid_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="currency">Currency</label>
      <select id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror">
          <option value="IDR" {{ old('currency', $quotation->currency ?? 'IDR') == 'IDR' ? 'selected' : '' }}>IDR</option>
          <option value="USD" {{ old('currency', $quotation->currency ?? 'IDR') == 'USD' ? 'selected' : '' }}>USD</option>
      </select>
      @error('currency')
          <div class="invalid-feedback">{{ $message }}</div>
      @enderror
  </div>

  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="customer_id">Customer</label>
      <select id="customer_id" name="customer_id" class="form-control select2 @error('customer_id') is-invalid @enderror" required>
        <option value="">-- Select Customer --</option>
        @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ (int)old('customer_id',$quotation->customer_id) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
      </select>
      @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <div class="mt-2">
        <label for="customer_contact_id">PIC</label>
        <select id="customer_contact_id" class="form-control select2" data-placeholder="-- Select PIC --">
          <option value="">-- Select PIC --</option>
        </select>
      </div>
    </div>
  </div>

  <input type="hidden" name="tax_pct" value="0">
  <input type="hidden" name="discount_amt" value="0">

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="attention">Attention (PIC)</label>
      <input readonly type="text" id="attention" name="attention" class="form-control @error('attention') is-invalid @enderror" value="{{ old('attention',$quotation->attention) }}">
      @error('attention')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-6">
      <label for="customer_phone">Customer Phone</label>
      <input readonly type="text" id="customer_phone" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone',$quotation->customer_phone) }}">
      @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="payment_term_id">Payment Terms</label>
      <select id="payment_term_id" name="payment_term_id" class="form-control select2 @error('payment_term_id') is-invalid @enderror">
        <option value="">-- Select Payment Term --</option>
        @foreach(($paymentTerms ?? []) as $pt)
          <option value="{{ $pt->id }}" {{ (int)old('payment_term_id', $quotation->payment_term_id) === $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
        @endforeach
      </select>
      @error('payment_term_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="terms_and_conditions_id">Terms & Conditions (Title)</label>
      <select id="terms_and_conditions_id" name="terms_and_conditions_id" class="form-control select2 @error('terms_and_conditions_id') is-invalid @enderror" data-placeholder="-- Select T&C --">
        <option value="">-- Select T&C --</option>
        @foreach(($tncList ?? []) as $t)
          <option value="{{ $t->id }}" {{ (int)old('terms_and_conditions_id', $quotation->terms_and_conditions_id) === $t->id ? 'selected' : '' }}>{{ $t->title }}{{ $t->version ? ' ('.$t->version.')' : '' }}</option>
        @endforeach
      </select>
      @error('terms_and_conditions_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <small class="form-text text-muted">Pilih T&C untuk disalin ke isi di bawah. Anda dapat mengedit teks hasil salinan bila diperlukan.</small>
    </div>
    <div class="form-group col-md-6">
      <label class="d-flex justify-content-between align-items-center mb-1">
        <span>T&C Preview</span>
        <button type="button" id="btn-use-tnc" class="btn btn-sm btn-outline-primary">Use Selected</button>
      </label>
      <pre id="tnc_preview" class="form-control" style="height: 150px; overflow:auto; white-space: pre-wrap;"></pre>
    </div>
  </div>

  <div class="form-group">
    <div class="d-flex justify-content-between align-items-center">
      <label for="terms_conditions" class="mb-1">Terms & Conditions</label>
      @if($isEdit)
      <button type="button" id="btn-refresh-tnc" class="btn btn-sm btn-outline-secondary">Re-apply selected T&C</button>
      @endif
    </div>
    <textarea id="terms_conditions" name="terms_conditions" rows="5" class="form-control @error('terms_conditions') is-invalid @enderror" placeholder="Terms & Conditions">{{ old('terms_conditions',$quotation->terms_conditions) }}</textarea>
    @error('terms_conditions')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  @push('scripts')
  <script>
    $(function(){
      const $cust = $('#customer_id');
      const $att = $('#attention');
      const $phone = $('#customer_phone');
      const $picSel = $('#customer_contact_id');

      // T&C selection only
      const $tncSel = $('#terms_and_conditions_id');
      const $tncPrev = $('#tnc_preview');
      const $tncBody = $('#terms_conditions');
      const $btnUseTnc = $('#btn-use-tnc');

      function showSelectedTnc(){
        const opt = $tncSel[0].options[$tncSel[0].selectedIndex];
        const body = opt ? (opt.getAttribute('data-body') || '') : '';
        $tncPrev.text(body);
      }

      $tncSel.on('change', showSelectedTnc);
      $btnUseTnc.on('click', function(){
        const opt = $tncSel[0].options[$tncSel[0].selectedIndex];
        if (!opt) return;
        const body = opt.getAttribute('data-body') || '';
        if (body) $tncBody.val(body);
      });

      function applyContact(){
        const opt = $picSel[0].options[$picSel[0].selectedIndex];
        if (!opt) return;
        const name = opt.dataset.name || '';
        const phone = opt.dataset.phone || '';
        if (name) $att.val(name);
        if (phone) $phone.val(phone);
      }

      function loadContacts(){
        const custId = $cust.val();
        $picSel.empty().append($('<option/>',{value:'',text:'-- Select PIC --'}));
        if (!custId) { return; }
        $.getJSON(`{{ url('customers') }}/${custId}/contacts`, function(res){
          const items = (res && res.items) ? res.items : [];
          let selected = '';
          items.forEach(function(it){
            const opt = $('<option/>',{value:it.id, text: it.name});
            opt.attr('data-name', it.name || '');
            if (it.phone) opt.attr('data-phone', it.phone);
            if (!selected && it.is_default) selected = it.id;
            $picSel.append(opt);
          });
          if (selected) $picSel.val(String(selected));
          applyContact();
        });
      }

      function applyCustomer(){
        // Load contacts and let default PIC populate the fields
        loadContacts();
      }

      // Prefill T&C preview (load all active T&C to get body via options endpoint if needed)
      // Since tncList is server-provided without body, we can lazy-load body for preview
      (function preloadTncBodies(){
        // If options endpoint supports filtering by service only, we can fallback to load all via server-side embedding.
        // For now, populate data-body attributes by fetching each item body via options endpoint without service filter.
        const opts = $tncSel[0].options;
        if (!opts || !opts.length) return;
        // Build a quick map from id->option
        const map = {};
        for (let i=0;i<opts.length;i++){ const o = opts[i]; if (o.value) map[o.value] = o; }
        $.getJSON(`{{ route('terms-conditions.options') }}`, function(res){
          const items = (res && res.items) ? res.items : [];
          items.forEach(function(it){
            const opt = map[String(it.id)];
            if (opt) opt.setAttribute('data-body', it.body || '');
          });
          showSelectedTnc();
        });
      })();

      $cust.on('change', applyCustomer);
      $picSel.on('change', applyContact);

      // Refresh T&C
      const $btnRefresh = $('#btn-refresh-tnc');
      if ($btnRefresh.length) {
        const urlRefresh = "{{ $isEdit ? route('quotations.refreshTnc',$quotation) : '' }}";
        const token = "{{ csrf_token() }}";
        $btnRefresh.on('click', function(){
          const $b = $(this); $b.prop('disabled', true).text('Re-applying...');
          const tncId = $tncSel.val() || '';
          $.post(urlRefresh, {_token: token, terms_and_conditions_id: tncId})
            .done(function(){ window.location.reload(); })
            .fail(function(){ alert('Gagal re-apply T&C'); })
            .always(function(){ $b.prop('disabled', false).text('Re-apply selected T&C'); });
        });
      }

      // Prefill on load
      applyCustomer();
    });
  </script>
  @endpush
  @if($locked)
  <div class="form-row">
    <div class="form-group col-md-3">
      <label>Status</label>
      <input type="text" class="form-control" value="{{ $quotation->status }}" disabled>
    </div>
    <div class="form-group col-md-3">
      <label>Subtotal</label>
      <input type="text" class="form-control" value="{{ number_format((float)$quotation->subtotal,2) }}" disabled>
    </div>
    <div class="form-group col-md-3">
      <label>Total</label>
      <input type="text" class="form-control" value="{{ number_format((float)$quotation->total,2) }}" disabled>
    </div>
  </div>
  @endif

  @if($locked)
    <div class="alert alert-warning mt-2">Quotation ini berstatus <strong>{{ $quotation->status }}</strong> dan bersifat <em>read-only</em>. Perubahan tidak dapat disimpan.</div>
  @endif
  <div class="mt-3">
    <button type="submit" class="btn btn-primary" {{ $locked ? 'disabled' : '' }}>Save</button>
    <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Back</a>
  </div>
</form>
