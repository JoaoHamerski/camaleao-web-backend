@extends('uniform-simulator.layout')

@section('title', 'Simulador de uniforme')

@section('content')
	<navbar></navbar>
	<div class="d-flex flex-row">
		<sidebar>
			<sidebar-item>
				<template #icon><i class="fas fa-palette fa-fw"></i></template>
				<template #label>CORES</template>
				<template #content>
					<sidebar-item-colors></sidebar-item-colors>
				</template>
			</sidebar-item>

			<sidebar-item>
				<template #icon><i class="fas fa-font fa-fw"></i></template>
				<template #label>NOME E NÚMERO</template>
				<template #content>
					<sidebar-item-name-and-number></sidebar-item-name-and-number>
				</template>
			</sidebar-item>
		</sidebar>
		
		<div class="uniform-container">
			<uniform>
				<template #back-base-svg>
					{!! file_get_contents('images/uniform-simulator/back-base.svg') !!}
				</template>
			</uniform>
		</div>
	</div>
@endsection