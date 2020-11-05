<nav class="sidebar {{ Cookie::get('sidebar_active') ? 'is-active' : '' }} bg-dark ">
	<div class="header mt-3 px-3">
    <i class="fas fa-user-circle fa-3x mb-3"></i>
		<h5 class="font-weight-bold">{{ Auth::user()->name }}</h5>
    <div class="small">{{ Auth::user()->role->name }}</div>

	 <hr style="border-color: rgba(255, 255, 255, .4)">
	</div>

  <div class="accordion accordion-sidebar" id="accordionSidebar">
    <ul class="list-group-flush list-unstyled">
      <li class="{{ Request::is(['/', 'cliente/*']) ? 'active' : '' }}">
        <a href="{{ route('clients.index') }}"><i class="fas fa-list fa-fw mr-1 text-primary"></i>Clientes</a>
      </li>

      @role(['gerencia', 'atendimento'])
      <li class="{{ Request::is('pedidos') ? 'active' : '' }}">
        <a href="{{ route('orders.index') }}"><i class="fas fa-boxes fa-fw mr-1 text-primary"></i>Pedidos</a>
      </li>
      @endrole
      
      @role(['gerencia', 'atendimento'])
      <li class="position-relative">
        <a class="{{ Request::is(['despesas*', 'fluxo-de-caixa*']) ?: 'collapsed' }}"
          data-toggle="collapse" 
          href="#collapseFinancial" 
          aria-expanded="false" 
          aria-controls="collapseFinancial">
            <i class="fas fa-dollar-sign fa-fw text-primary"></i> Financeiro

          <div class="icon">
            <i class="fas fa-chevron-down"></i>
          </div>
        </a>

        <div id="collapseFinancial" class="collapse {{ Request::is(['despesas*', 'fluxo-de-caixa*']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <ul class="list-group-flush list-unstyled">
            @role(['gerencia', 'atendimento'])
            <li class="{{ Request::is('despesas*') ? 'active' : '' }}">
              <a href="{{ route('expenses.index') }}"><i class="fas fa-funnel-dollar fa-fw mr-1 text-primary"></i>Despesas</a>
            </li>
            @endrole

            @role('gerencia')
            <li class="{{ Request::is('fluxo-de-caixa*') ? 'active' : '' }}">
              <a href="{{ route('cash-flow.index') }}"><i class="fas fa-cash-register fa-fw mr-1 text-primary"></i>Fluxo de caixa</a>
            </li>
            @endrole
          </ul>
        </div>
      </li>
      @endrole

      @role('gerencia')
      <li class="{{ Request::is('usuarios*') ? 'active' : '' }}">
          <a href="{{ route('users.index') }}"><i class="fas fa-users fa-fw mr-1 text-primary"></i>Usuários</a>
      </li>
      @endrole

      <li class="{{ Request::is('minha-conta*') ? 'active' : '' }}">
        <a href="{{ route('users.my-account') }}"><i class="fas fa-user fa-fw mr-1 text-primary"></i>Minha conta</a>
      </li>

      @role('gerencia')
      <li class="{{ Request::is('atividades*') ? 'active' : '' }}">
        <a href="{{ route('activities.index') }}"><i class="fas fa-clipboard-list fa-fw mr-1 text-primary"></i>Atividades</a>
      </li>
      @endrole

      <li>
        <a href="{{ route('auth.logout') }}"><i class="fas fa-sign-out-alt mr-1 text-primary"></i>Sair</a>
      </li>
    </ul>
  </div>
  </nav>