<li class="list-group-item">
	<div class="first-upper-case">
	  <strong>{{ __($field) }}</strong>
	</div>

	@if (in_array($field, ['name', 'city', 'code', 'quantity', 'note', 'description', 'employee_name']))
		@if (is_object($field) && $field === 'city') 
			De <strong>{{ $changes['old'][$field] ? '[vazio]' : $changes['old'][$field]->name }}</strong>
			para <strong>{{ $changed ? '[vazio]' : $changed }}</strong> 
		@else
			De <strong>{{ $changes['old'][$field] ? '[vazio]' : $changes['old'][$field] }}</strong>
			para <strong>{{ $changed ? '[vazio]' : $changed }}</strong> 
		@endif
	@endif

	@if (in_array($field, ['phone']))
		De <strong>{{ empty($changes['old'][$field]) ? '[vazio]' : Mask::phone($changes['old'][$field]) }}</strong>
		para <strong>{{ empty($changed) ? '[vazio]' : Mask::phone($changed) }}</strong>
	@endif

	@if (in_array($field, ['money']))
		De <strong>{{ empty($changes['old'][$field]) ? '[vazio]' : Mask::money($changes['old'][$field])  }}</strong>
		para <strong>{{ empty($changed) ? '[vazio]' : Mask::money($changed) }}</strong>
	@endif

	@if (fnmatch('*date', $field))
		De <strong>{{ empty($changes['old'][$field]) ? '[vazio]' : Helper::date($changes['old'][$field], '%d/%m/%Y') }}</strong>
		para <strong>{{ empty($changed) ? '[vazio]' : Helper::date($changed, '%d/%m/%Y') }}</strong>
	@endif

	@if (in_array($field, ['status_id']))
		De <strong>{{\App\Models\Status::find($changes['old'][$field])->text}}</strong> 
		para <strong>{{ \App\Models\Status::find($changed)->text }}</strong>
	@endif

	@if (in_array($field, ['payment_via_id', 'expense_via_id']))
		De <strong>{{ \App\Models\Via::find($changes['old'][$field])->name ?? '[não informado]' }}</strong>
		para <strong>{{ \App\Models\Via::find($changed)->name ?? '[não informado]' }}</strong>
	@endif

	@if (in_array($field, ['expense_type_id']))
		De <strong>{{ \App\Models\ExpenseType::find($changes['old'][$field])->name ?? '[não informado]' }}</strong>
		para <strong>{{ \App\Models\ExpenseType::find($changed)->name ?? '[não informado]' }}</strong>
	@endif

	@if (in_array($field, ['closed_at']))
		De <strong>{{ $changes['old'][$field] == null ? 'Aberto' : 'Fechado' }}</strong>
		para <strong>{{ $changed == null ? 'Aberto' : 'Fechado' }}</strong>
	@endif

	@if (in_array($field, ['receipt_path', 'payment_voucher_paths']))
		@empty($changed)
			Comprovante de pagamento excluído
		@else
			Comprovante de pagamento alterado
		@endempty
	@endif

	@if (in_array($field, ['art_paths', 'size_paths']))
		@empty($changed)
			Imagens da arte excluídas
		@else
			Imagens da arte alterada
		@endempty	
	@endif

	
	@if (in_array($field, ['is_confirmed']))
		Pedido <strong>{{ $changes['old'][$field] ? 'confirmado' : 'rejeitado' }}</strong>
	@endif
</li>
