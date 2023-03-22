<template>
	<div>
		<div class="my-page-title">
			<div class="row">
				<div class="col-md-8">
					<p class="h2">{{title}}</p>
				</div>
				<div class="col-md-4 text-right">
					<slot></slot>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	
</style>

<script>
	export default{
		props:['title']
	}
</script>