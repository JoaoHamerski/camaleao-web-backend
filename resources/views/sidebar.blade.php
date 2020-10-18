<nav class="sidebar {{ Cookie::get('sidebar_active') ? '' : 'is-active' }} bg-dark ">
	<div class="header mt-3 px-3">
    <i class="fas fa-user-circle fa-3x mb-3"></i>
		<h5 class="font-weight-bold">{{ Auth::user()->name }}</h5>

	 <hr style="border-color: rgba(255, 255, 255, .4)">
	</div>

  <div>
    <ul class="list-group-flush list-unstyled">
      <li class="{{ Request::is('cliente/*') ? 'active' : '' }}">
        <a href="{{ route('clients.index') }}"><i class="fas fa-list fa-fw mr-1 text-primary"></i>Clientes</a>
      </li>

      <li class="{{ Request::is('pedidos') ? 'active' : '' }}">
        <a href="{{ route('orders.index') }}"><i class="fas fa-boxes fa-fw mr-1 text-primary"></i>Pedidos</a>
      </li>

      <li class="">
        <a href="{{ route('auth.logout') }}"><i class="fas fa-sign-out-alt mr-1 text-primary"></i>Sair</a>
      </li>
    </ul>
  </div>

  <form id="logoutForm" method="POST" action="{{ route('auth.logout') }}">
    @csrf
  </form>
</nav>