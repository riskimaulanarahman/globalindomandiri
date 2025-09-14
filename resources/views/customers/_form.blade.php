@php($isEdit = $customer && $customer->exists)

<form method="POST" action="{{ $isEdit ? route('customers.update',$customer) : route('customers.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="form-row">
    <div class="form-group col-md-4">
      <label for="code">Code</label>
      <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code',$customer->code) }}" placeholder="Auto if blank">
      @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-8">
      <label for="name">Name</label>
      <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$customer->name) }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="npwp">NPWP</label>
      <input type="text" name="npwp" id="npwp" class="form-control @error('npwp') is-invalid @enderror" value="{{ old('npwp',$customer->npwp) }}">
      @error('npwp')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="payment_term_days">Payment Term (days)</label>
      <input type="number" min="0" max="365" name="payment_term_days" id="payment_term_days" class="form-control @error('payment_term_days') is-invalid @enderror" value="{{ old('payment_term_days',$customer->payment_term_days) }}">
      @error('payment_term_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="credit_limit">Credit Limit</label>
      <input type="number" step="0.01" min="0" name="credit_limit" id="credit_limit" class="form-control @error('credit_limit') is-invalid @enderror" value="{{ old('credit_limit',$customer->credit_limit) }}">
      @error('credit_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
      <label for="notes">Notes</label>
      <input type="text" name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes',$customer->notes) }}">
      @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>


  <hr>
  <h5>Contacts (PIC)</h5>
  <div class="table-responsive">
    <table class="table table-bordered table-sm" id="contacts_table">
      <thead>
        <tr>
          <th style="width:20%">Name</th>
          <th style="width:18%">Phone</th>
          <th style="width:22%">Email</th>
          <th style="width:22%">Address</th>
          <th style="width:8%">Default</th>
          <th>Notes</th>
          <th style="width:8%">Actions</th>
        </tr>
      </thead>
      <tbody>
        @php($oldContacts = old('contacts'))
        @if($oldContacts)
          @foreach($oldContacts as $oc)
            <tr>
              <td><input type="text" name="contacts[{{ $loop->index }}][name]" class="form-control" value="{{ $oc['name'] ?? '' }}"></td>
              <td><input type="text" name="contacts[{{ $loop->index }}][phone]" class="form-control" value="{{ $oc['phone'] ?? '' }}"></td>
              <td><input type="email" name="contacts[{{ $loop->index }}][email]" class="form-control" value="{{ $oc['email'] ?? '' }}"></td>
              <td><input type="text" name="contacts[{{ $loop->index }}][address]" class="form-control" value="{{ $oc['address'] ?? '' }}" placeholder="PIC address"></td>
              <td class="text-center"><input type="checkbox" name="contacts[{{ $loop->index }}][is_default]" value="1" {{ !empty($oc['is_default']) ? 'checked' : '' }}></td>
              <td><input type="text" name="contacts[{{ $loop->index }}][notes]" class="form-control" value="{{ $oc['notes'] ?? '' }}"></td>
              <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeContactRow(this)">Remove</button></td>
            </tr>
          @endforeach
        @else
          @foreach(($customer->contacts ?? []) as $cc)
            <tr>
              <td><input type="text" name="contacts[{{ $loop->index }}][name]" class="form-control" value="{{ $cc->name }}"></td>
              <td><input type="text" name="contacts[{{ $loop->index }}][phone]" class="form-control" value="{{ $cc->phone }}"></td>
              <td><input type="email" name="contacts[{{ $loop->index }}][email]" class="form-control" value="{{ $cc->email }}"></td>
              <td><input type="text" name="contacts[{{ $loop->index }}][address]" class="form-control" value="{{ $cc->address }}"></td>
              <td class="text-center"><input type="checkbox" name="contacts[{{ $loop->index }}][is_default]" value="1" {{ $cc->is_default ? 'checked' : '' }}></td>
              <td><input type="text" name="contacts[{{ $loop->index }}][notes]" class="form-control" value="{{ $cc->notes }}"></td>
              <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeContactRow(this)">Remove</button></td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </div>
  <button type="button" class="btn btn-sm btn-outline-primary" onclick="addContactRow()"><i class="fas fa-plus mr-1"></i> Add Contact</button>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>

@push('scripts')
<script>
  function removeContactRow(btn){
    const tr = btn.closest('tr');
    tr.parentNode.removeChild(tr);
    renumberContacts();
  }
  function addContactRow(){
    const tbody = document.querySelector('#contacts_table tbody');
    const idx = tbody.querySelectorAll('tr').length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input type=\"text\" name=\"contacts[${idx}][name]\" class=\"form-control\" placeholder=\"Full name\"></td>
      <td><input type=\"text\" name=\"contacts[${idx}][phone]\" class=\"form-control\" placeholder=\"Phone\"></td>
      <td><input type=\"email\" name=\"contacts[${idx}][email]\" class=\"form-control\" placeholder=\"Email\"></td>
      <td><input type=\"text\" name=\"contacts[${idx}][address]\" class=\"form-control\" placeholder=\"Address\"></td>
      <td class=\"text-center\"><input type=\"checkbox\" name=\"contacts[${idx}][is_default]\" value=\"1\"></td>
      <td><input type=\"text\" name=\"contacts[${idx}][notes]\" class=\"form-control\" placeholder=\"Notes\"></td>
      <td class=\"text-center\"><button type=\"button\" class=\"btn btn-sm btn-outline-danger\" onclick=\"removeContactRow(this)\">Remove</button></td>
    `;
    tbody.appendChild(tr);
  }
  function renumberContacts(){
    const rows = document.querySelectorAll('#contacts_table tbody tr');
    rows.forEach((tr, i) => {
      tr.querySelectorAll('input').forEach(inp => {
        const name = inp.getAttribute('name');
        inp.setAttribute('name', name.replace(/contacts\[[0-9]+\]/, `contacts[${i}]`));
      });
    });
  }
</script>
@endpush

