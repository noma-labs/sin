<template id="categorie">
	<div>
		<div class="row">
				<div class="col-md-3">Categoria</div>
				<div class="col-md-3">Data rilascio</div>
				<div class="col-md-3">Data scadenza</div>
				<div class="col-md-3">Restrizioni</div>
		</div>
		<div class="row" v-for="categoria in categorie">
			<div class="col-md-3">{{categoria.categoria}}	</div>
			<div class="col-md-3">
				<date-picker :value="categoria.pivot.data_rilascio" placeholder="Selezionare una data" :language="language" :format="customFormatter"></date-picker>
			</div>
			<div class="col-md-3">
				<date-picker :value="categoria.pivot.data_scadenza" placeholder="Selezionare una data" :language="language" :format="customFormatter"></date-picker>
			</div>
			<div class="col-md-3">{{categoria.pivot.restrizione_codice}}	</div>
		</div>
		<div class="row">
			<a class="btn btn-primary col-md-4 offset-md-8" @click="aggiungiCategoria">Aggiungi categoria</a>
		</div>
			<!-- Modal Aggiungi Lavoratore -->
		    <transition name="modal">
		        <div class="modal-mask"  @click="close" v-show="showModalAggiungiCategoria">
		            <div class="modal-container"  @click.stop>
		                <div class="modal-header">
		                    <h3>Aggiungi Categoria</h3>
		                </div>
		                <div class="modal-body ">
							<div class="form-group row">
								<div class="col-md-6">
									<label>Categoria</label>
								</div>
								<div class="col-md-6">
									<select class="form-control" v-model="selected">
										<option v-for="categoria in categoriePossibili" v-bind:value="categoria.id">
											{{ categoria.categoria }} - {{categoria.descrizione}}
										</option>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6">
									<label>Categoria rilasciata il:</label>
								</div>
								<div class="col-md-6">
									<date-picker  placeholder="Selezionare una data" :language="language" :format="customFormatter"></date-picker>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6">
									<label>Categoria valida fino al:</label>
								</div>
								<div class="col-md-6">
								
									<date-picker placeholder="Selezionare una data" :language="language" :format="customFormatter"></date-picker>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<label>Restrizione</label>
								</div>
								<div class="col-md-6"> 
									<input type="text" class="form-control" >
								</div>
							</div>
								
						</div>
		                <div class="modal-footer text-right">
		                </div>
		            </div>
		        </div>
		    </transition>
	</div>
</template>

<script>
	import {it} from 'vuejs-datepicker/dist/locale'

	export default {
		props: ['numero_patente'],
		data: function() {
			return {
				language: it,
				categorie:[],
				selected: '0',
				categoriePossibili: [],
				showModalAggiungiCategoria: false,
			};
		},
		created: function(){
			if(this.numero_patente){
				axios.get("/api/patente/"+this.numero_patente+"/categorie").then(response => {
					this.categorie = response.data.categorie;
					console.log(this.categorie);
				});
			}
			axios.get("/api/patente/GR2110358W/categorie?filtro=possibili").then(response => {
				this.categoriePossibili = response.data;
				console.log(this.categoriePossibili);
			});
		},
		methods:{
			customFormatter(date) {
		      return moment(date).format('YYYY-MM-DD');
			},
			formatData: function(date){
		    	return moment(date).format('YYYY-MM-DD');
			},
			aggiungiCategoria: function(){
				this.showModalAggiungiCategoria = true;
			},
			close: function () {
		        this.showModalAggiungiCategoria=false;
		    },
		}
	}
</script>


<style scoped>
	* {
	    box-sizing: border-box;
	}

	.modal-mask {
	    position: fixed;
	    z-index: 9998;
	    top: 0;
	    left: 0;
	    width: 100%;
	    height: 100%;
	    background-color: rgba(0, 0, 0, .5);
	    transition: opacity .3s ease;
	}

	.modal-container {
	    width: 700px;
	    margin: 40px auto 0;
	    background-color: #fff;
	    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
	    transition: all .3s ease;
	    font-family: Helvetica, Arial, sans-serif;
	}

	.modal-header {
		background: #1D70B8;
	}

	.modal-footer {
		background: #1D70B8;
	}

	.modal-header h3 {
	    margin-top: 0;
	    color: #fff;
	}

	.modal-body {
	    margin: 20px 0;
	}

	.text-right {
	    text-align: right;
	}

	.form-label {
	    display: block;
	    margin-bottom: 1em;
	}

	.form-label > .form-control {
	    margin-top: 0.5em;
	}

	.form-control {
	    display: block;
	    width: 100%;
	    padding: 0.5em 1em;
	    line-height: 1.5;
	    border: 1px solid #ddd;
	}

	/*
	 * The following styles are auto-applied to elements with
	 * transition="modal" when their visibility is toggled
	 * by Vue.js.
	 *
	 * You can easily play with the modal transition by editing
	 * these styles.
	 */

	.modal-enter {
	  opacity: 0;
	}

	.modal-leave-active {
	  opacity: 0;
	}

	.modal-enter .modal-container,
	.modal-leave-active .modal-container {
	  -webkit-transform: scale(1.1);
	  transform: scale(1.1);
	}
</style>