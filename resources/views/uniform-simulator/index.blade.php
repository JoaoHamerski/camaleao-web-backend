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

			<sidebar-item width="270px">
				<template #icon><i class="fas fa-font fa-fw"></i></template>
				<template #label>NOME E NÚMERO</template>
				<template #content>
					<sidebar-item-name-and-number></sidebar-item-name-and-number>
				</template>
			</sidebar-item>

			<sidebar-item width="400px">
				<template #icon><i class="fas fa-shield-alt fa-fw"></i></template>
				<template #label>ESCUDO</template>
				<template #content>
					<sidebar-item-shield></sidebar-item-shield>
				</template>
			</sidebar-item>

			<sidebar-item width="270px">
				<template #icon><i class="fas fa-upload fa-fw"></i></template>
				<template #label>ADICIONAR IMAGENS</template>
				<template #content>
					<sidebar-item-import-images></sidebar-item-import-images>
				</template>
			</sidebar-item>
		</sidebar>
		
		<div class="uniform-container no-select">
			<uniform>
				<template #back-base-svg>
					{!! file_get_contents('images/uniform-simulator/back-base.svg') !!}
				</template>
				<template #front-base-svg>
					{!! file_get_contents('images/uniform-simulator/front-base.svg') !!}
				</template>
				<template #back-neck-base-svg>
					{!! file_get_contents('images/uniform-simulator/back-neck-base.svg') !!}
				</template>
				<template #front-neck-base-svg>
					{!! file_get_contents('images/uniform-simulator/front-neck-base.svg') !!}
				</template>
				<template #brand-svg>
					{!! file_get_contents('images/uniform-simulator/camaleao.svg') !!}
				</template>
				<template #shield-svg>
					{!! file_get_contents('images/uniform-simulator/shield.svg') !!}
				</template>
			</uniform>
		</div>

		<sidebar-attachs>
			
		</sidebar-attachs>
	</div>
@endsection