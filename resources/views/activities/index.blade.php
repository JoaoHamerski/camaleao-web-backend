@extends('layout')

@section('title', 'Log de atividades')

@section('content')
  <div class="col-md-10 mx-auto mt-5">
    <div class="card">
      <div class="card-header bg-success text-white font-weight-bold position-relative">
        <a class="stretched-link {{ Request::anyFilled(['usuario', 'entidade', 'data']) ? '' : 'collapsed' }}" 
          data-toggle="collapse" 
          href="#collapse-card" 
          aria-expanded="true">
        </a>

        <div class="card-collapse">
          <i class="fas fa-filter fa-fw mr-1"></i>Filtrar atividades
          <div class="collapse-icon">
                  <i class="fas fa-caret-down fa-fw fa-2x"></i>
            </div>
        </div>
      </div>

      <div id="collapse-card" class="collapse {{ Request::anyFilled(['usuario', 'entidade', 'data']) ? 'show' : '' }}">
        <div class="card-body">
          <form action="{{ route('activities.index') }}" method="GET">
            <div class="form-row d-flex flex-column flex-md-row">
              <div class="form-group col">
                <label for="usuario" class="font-weight-bold">Usuário</label>
                <select class="custom-select" name="usuario" id="usuario">
                  <option value="">Selecione um usuário</option>
                  @foreach($users as $user)
                    <option value="{{ $user->id }}"
                      @if (Request::query('usuario') == $user->id) selected="selected" @endif
                    >
                      {{ $user->name }}
                    </option>
                  @endforeach
                </select>
                <small class="text-secondary">Você pode filtrar apenas por um ou combinar os campos</small>
              </div>

              <div class="form-group col">
                <label class="font-weight-bold" for="entidade">Atividades em: </label>
                <select name="entidade" id="entidade" class="custom-select">
                  <option value="">Selecione uma entidade</option>
                  @foreach($entities as $entity)
                    <option value="{{ $entity }}"
                      @if (Request::query('entidade') == $entity) selected="selected" @endif
                    >
                    {{ __($entity) }}
                    </option>
                  @endforeach 
                </select>
              </div>
            </div>

            <div class="form-row d-flex flex-column flex-md-row">
              <div class="form-group col">
                <label for="data" class="font-weight-bold">Data</label>
                <input type="text"
                  data-toggle="datepicker" 
                  name="data" 
                  id="data"
                  autocomplete="off" 
                  value="{{ Request::query('data') }}" 
                  class="form-control" 
                  placeholder="dd/mm/aaaa">
              </div>
            </div>

            <button type="submit" class="btn btn-success">Filtrar</button>
            <a href="{{ route('activities.index') }}" class="btn btn-outline-success">Zerar filtros</a>
          </form>
        </div>
      </div>
    </div>  
  </div>

  <div class="col-md-10 mx-auto">
    <div class="card mt-2">
      <div class="card-header bg-primary text-white font-weight-bold position-relative">
        <a href="{{ route('activities.index') }}" class="stretched-link"></a>
        <i class="fas fa-clipboard-list fa-fw mr-1"></i>Atividades feitas no sistema
      </div>

      <div class="card-body">
        <ul class="list-group list-group-flush">
          @forelse($activities as $activity)
            @if ($activity->subject_type == \App\Models\Client::class)
              @include('activities.list-item', ['viewName' => 'client'])
            @endif

            @if ($activity->subject_type == \App\Models\Order::class)
              @include('activities.list-item', ['viewName' => 'order'])
            @endif

            @if ($activity->subject_type == \App\Models\Note::class)
              @include('activities.list-item', ['viewName' => 'note'])
            @endif

            @if ($activity->subject_type == \App\Models\Payment::class)
              @include('activities.list-item', ['viewName' => 'payment'])
            @endif

            @if ($activity->subject_type == \App\Models\Expense::class)
              @include('activities.list-item', ['viewName' => 'expense'])
            @endif

            @if ($activity->subject_type == \App\Models\ExpenseType::class)
              @include('activities.list-item', ['viewName' => 'expense-type'])
            @endif
          @empty
            <li class="list-group-item">
              <h5 class="text-secondary text-center">Nenhuma atividade que corresponda à busca</h5>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="mt-2">
      {{ $activities->links() }}
    </div>
  </div>
@endsection

@push('css')
  <link rel="stylesheet" href="{{ mix('css/_date-picker.css') }}">
@endpush

@push('script')
  <script src="{{ mix('js/_date-picker.js') }}"></script>
  <script>
    applyCleave($('[name=data]'), cleaveDate);
  </script>
@endpush