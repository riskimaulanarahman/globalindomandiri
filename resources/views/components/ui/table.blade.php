<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">{{ $title ?? 'List' }}</h6>
    @isset($actions)
      <div>{{ $actions }}</div>
    @endisset
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm table-hover table-striped table-bordered align-middle table-sticky" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            @foreach(($headers ?? []) as $h)
              <th>{{ $h }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          {{ $slot }}
        </tbody>
      </table>
    </div>
  </div>
</div>
