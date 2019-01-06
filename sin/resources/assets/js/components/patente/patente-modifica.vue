<template id="categorie">
	<div>
		<div class="alert "  v-show="showAlert" v-bind:class="[hasError ? 'alert-danger' : '', 'alert-success']" role="alert"> 
			{{alertMessage}} </strong>
		</div>

		<div class="row">
			<div class="col-md-6">
				<slot name="persona-info">
					
				</slot>
				<div class="row">
					<div class="col-md-6">
						<label for="data_rilascio_patente">Patente rilasciata il:</label>
						<date-picker name="data_rilascio_patente" 
									@selected="selectData_rilascio_patente" 
									:bootstrap-styling="true" 
									:language="language" 
									:format="customFormatter"
									:value="nuovaPatente.data_rilascio_patente"
									:disabled="disabledAll">
						</date-picker>
					</div>
					
					<div class="col-md-6">
						<label for="rilasciata_dal">Rilasciata da:</label>
						<!-- <input type="text" class="form-control"  
											v-model="nuovaPatente.rilasciata_dal" id="rilasciata_dal" 
											name="rilasciata_dal" 
											:disabled=disabledAll> -->
						<v-select
				            :options="rilascioOptions"
							:debounce="500"
							:on-search="getRilascio"
							name="rilasciata_dal" 
							v-model="nuovaPatente.rilasciata_dal"
							label="rilasciata_dal"
							index="rilasciata_dal"
							:disabled="disabledAll"
							taggable
							>
							<span slot="no-options">Non torvato</span>
						</v-select>
					</div>
					
				</div><!-- end secoond row in left colum-->
				<div class="row">
					<div class="col-md-6">
							<label for="data_scadenza_patente">Patente valida fino al:</label>
							<date-picker name="data_scadenza_patente" 
										@selected="selectData_scadenza_patente"
										:bootstrap-styling="true" 
										:language="language" 
										:disabledDates="disabledData_scadenza_patente"
										:format="customFormatter"
										:value="nuovaPatente.data_scadenza_patente"
										:disabled="disabledAll">
							</date-picker>
						</div>
					<div class="col-md-6">
						<label for="numero_patente">Numero Patente:</label>
						<input type="text" class="form-control" v-model="nuovaPatente.numero_patente" name="numero_patente" :disabled=disabledAll>
					</div>
				</div><!-- end third row in left colum-->
				<div class="row">
					<div class="form-group col-md-12">
						<label for="note">Note:</label>
						<textarea class="form-control" v-model="nuovaPatente.note" name="note" :disabled=disabledAll></textarea>
					</div>
				</div> <!-- end fouth row in left colum-->
				<div class="row">
					<div class="col-md-12">
						<div class="form-check">
								<input class="form-check-input" type="checkbox" name="assegnaCommissione"  true-value="commissione" false-value="null" v-model="nuovaPatente.stato" id="ycommissione" >
								<label class="form-check-label" for="ycommissione">
									Con commissione.
								</label>
						</div>
					</div>
				</div> <!-- end fifth row in left colum-->
				<div class="row my-2">
					<div class="col-md-3">
						<my-modal modal-title="Eliminazione patente" button-title="Elimina">
							<template slot="modal-body-slot">
							Vuoi davvero eliminare la patente di
							{{nuovaPatente.numero_patente}}
							</template>

							<template slot="modal-button">
							<a class="btn btn-danger" :href="webPatenteElimina" >Elimina</a>
							</template>
						</my-modal>
					</div>
					<div class="col-md-3">
						<button type="submit" 
								@click="salvaNuovaPatente"
								:disabled="disabledSalvaNuovaPatente || disabledAll" 
								form="edit-patente" 
								class="btn btn-primary">
								Salva
						</button>
					</div>
				</div> <!-- end fouth row in left colum-->
			</div>  <!-- end left column-->

			<div class="col-md-6">
				<div class="row">
					<div class="col-md-5">
						<div class="card">
							<h5 class="card-header">Categorie</h5>
							<div class="card-body">
								<div v-if="nuovaPatente.categorie.length === 0" class="p-1 mb-2 bg-danger text-white">Nessuna categoria </div>
								<ul class="list-inline">
									<li class="list-inline-item" v-for="(cat, index) in nuovaPatente.categorie">
										<span class="badge badge-primary">{{ cat.categoria }} </span>
									</li>
								</ul>
								<button class="btn btn-warning mt-3" @click="openAggiungiCategoria" :disabled=disabledAll>Aggiungi</button>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<div class="card">
							<h5 class="card-header">C.Q.C</h5>
							<div class="card-body">
								<div v-if="nuovaPatente.cqc.length === 0" class="p-1 mb-2 bg-danger text-white">Nessun C.Q.C </div>
									<div v-for="(cqc, index) in nuovaPatente.cqc">
										<b>{{ cqc.categoria }}: </b>  
										<span class="badge badge-success">{{ cqc.pivot.data_rilascio }} </span> 
										<span class="badge badge-danger">{{ cqc.pivot.data_scadenza }}</span> 
									</div>
								<button class="btn btn-warning mt-3" @click="openAggiungiCQC">Aggiungi</button>
							</div>
						</div>
					</div>
				</div>
			 </div>  <!-- end  rigth column -->
		</div> <!-- end first row -->

			<!-- modal aggiungi categoria -->
		<transition name="modal">
			<div class="modal-mask"  @click="close" v-show="showModalAggiungiCategoria">
				<div class="modal-container"  @click.stop>
					<div class="modal-header">
						<h3>Aggiungi Categoria</h3>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-3" v-for="(categoria, index) in categoriePossibili">
								<div class="form-check">
									<input class="form-check-input" type="checkbox"  :value="categoria" v-model="nuovaPatente.categorie" id="defaultCheck1">
									<label class="form-check-label" for="defaultCheck1">{{ categoria.categoria }} </label>
								</div>
							</div>
						</div>  
					</div>
				<div class="modal-footer text-right">
						<a class="btn btn-success" href="#" role="button" @click="close">OK</a>
					</div>
				</div>
			</div>
		</transition>

		
		<!-- modal aggiungi CQC -->
		<transition name="modal">
			<div class="modal-mask" v-show="showModalAggiungiCQC">
				<div class="modal-container"  @click.stop>
					<div class="modal-header"><h3>Aggiungi CQC</h3>	</div>
					<div class="modal-body">	
					<div class="row" v-for="c in cqcPossibili">
							<div class="col-md-3"  >
								<div class="form-check">
									<input class="form-check-input" type="checkbox"  :value="c" v-model="nuovaPatente.cqc" id="checkCQC">
									<label class="form-check-label" for="checkCQC">{{ c.categoria }} </label>
								</div>
							</div>
							<div class="col-md-4">
								<label>Rilasciata il:</label>
								<date-picker :bootstrap-styling="true" 
										 	v-model="c.pivot.data_rilascio"
											placeholder="---Seleziona una data---" 
											:language="language" 
											:format="customFormatter"
											>
								</date-picker>
							</div>
							<div class="col-md-4">
								<label>Valida fino al:</label>
								<date-picker :bootstrap-styling="true" 
											v-model="c.pivot.data_scadenza"
											placeholder="---Seleziona una data---" 
											:language="language" 
											:format="customFormatter"
											>
								</date-picker> 
							</div>
						</div>  <!-- end row C.Q.C -->
					</div>
				    <div class="modal-footer text-right">
						<button class="btn btn-danger" @click="closeModalAggiungiCqc" >OK</button>
					</div>
				</div>
			</div>
		</transition>
		
	</div>
</template>

<script>
	import {it} from 'vuejs-datepicker/dist/locale'
    import vSelect from "vue-select"
	
	export default {
		components: {vSelect},
		props: [
			'numero_patente',
			'apiPatente',   
			'apiPatentePersone',
			'apiPatenteCategorie',
			'apiPatenteCqc',
			'apiPatenteModifica',
			'webPatenteElimina',
			'apiPatenteRilascio'
			],
		data: function() {
			return {
				disabledAll: false, // disable all the elements quando la patente è stata inserita correttamente.
				showAlert: false,  // if true the alert is shown 
				hasError : false, // if hasErroe=true the text-success alert message is shown else text-danger
				alertMessage: "",	// message to be shown in the alert
				optionsPersone: [],                         // list of the persone to be shown in the dropdown menu
				personaSelected: {							// persona selezionata dal dropdown menu
					persona_id : null,
					nomecognome:"",
					data_nascita: "",
					provincia_nascita :""
				},						
				personaPlaceholder: "---inserisci nominativo---",
				language: it,
				showModalAggiungiCQC: false,  		// if true modal dell'aggiunta del C.Q.C viene visualizzato
				showModalAggiungiCategoria: false,  // if true modal is shown
				categoriePossibili: [],             // categorie disponibili da assegnare alla patente
				cqcPossibili : [], 					// cqc possibili (persona e merci)
				nuovaPatente : {
					persona_id: null,
					numero_patente: null,
					rilasciata_dal :null,
					data_rilascio_patente : null,
					data_scadenza_patente : null,
					note : null,
					stato: null, 					// enum: 'commissione' o NULL
					categorie: [  					// array delle categorie assegnate alla patente
						// id: null
						// categoria: null,
						// descrizione: null,
						// note: null,
						// }
					],
					cqc:[]          	
				},
				rilascioOptions:[] // array con rilascita_dal
			};
		},
		created: function(){
			this.loadPatente();
			// axios.get(this.apiPatente).then(response => {
			// 	this.nuovaPatente = response.data;
			// 	this.sortCategorie();
			// });
			axios.get(this.apiPatenteCategorie).then(response => {
				this.categoriePossibili = response.data;
			});
			axios.get(this.apiPatenteCqc).then(response =>{
				this.cqcPossibili = response.data;
				this.cqcPossibili.forEach(cqc => { 
					// aggiunge la data_rilascio e data_scadenza agli oggetti cqc (altimento non funziona la select)
					// cqc.pivot = {data_rilascio : null,data_scadenza : null, categoria_patente_id:null, numero_patente:null};
					cqc.pivot = {categoria_patente_id: null,
								data_rilascio:null,
								data_scadenza:null,
								numero_patente:null}

				})
			});
		},
		computed:{
			disabledSalvaNuovaPatente: function(){
				return    this.nuovaPatente.persona_id === null
				 		|| this.nuovaPatente.numero_patente === null
						|| this.nuovaPatente.data_rilascio_patente === null
						|| this.nuovaPatente.data_scadenza_patente=== null 			
			},
			disabledData_scadenza_patente: function(){
				return {
					to: new Date(this.nuovaPatente.data_rilascio_patente)
				}
			},
			disabled_data_scadenza_categoria: function(){
				return {
					to: new Date(this.nuovaCategoria.data_rilascio)
				}
			}
		},
		methods:{
			getRilascio(search, loading) {
				loading(true)
				axios.get(this.apiPatenteRilascio, { params: { term: search } })
					.then(response => {
						this.rilascioOptions = response.data;
						loading(false)
					})
					.catch(error => {});
            },
			loadPatente: function(){
				axios.get(this.apiPatente).then(response => {
					this.nuovaPatente = response.data;
					this.sortCategorie();
				});
			},
			formatCQCDate(){
				this.nuovaPatente.cqc.forEach(cqc => { // aggiunge la data_rilascio e data_scadenza agli oggetti cqc.
					cqc.pivot.data_rilascio = this.customFormatter(cqc.pivot.data_rilascio);
					cqc.pivot.data_scadenza = this.customFormatter(cqc.pivot.data_scadenza);
				});
			},
			closeModalAggiungiCqc : function () {
				this.formatCQCDate();
				this.showModalAggiungiCQC=false;
			},
			openAggiungiCQC: function(){
				// copy the pivto table otherwise checkboxes do not work
				this.nuovaPatente.cqc.forEach(c_patente => {
					this.cqcPossibili.forEach(c_possibili =>{
						if (c_patente.id === c_possibili.id){
							c_possibili.pivot = c_patente.pivot;
						}
					})
				})
				console.log(this.cqcPossibili);
				this.showModalAggiungiCQC = true;
			},
			openAggiungiCategoria: function(){
				this.showModalAggiungiCategoria = true;
			},
			customFormatter(date) {
		      return moment(date).format('YYYY-MM-DD');
			},
			salvaNuovaPatente(){
				axios.put(this.apiPatenteModifica, this.nuovaPatente)
					.then(response=>{
						this.showAlert =true;
						this.alertMessage = response.data.msg;
						if (response.data.err === 0){ // {"err":0,"msg":"patente inserita correttamente"}
							this.hasError = false;
							this.disabledAll = true;
							axios.get(this.apiPatente).then(response => {
								this.nuovaPatente = response.data;
								this.disabledAll = false;
								this.sortCategorie();
							});
						}
						else // {"err":1,"msg":"patente inserita correttamente"}
							this.hasError= true;
					})
					.catch(error => {
						this.hasError= true;
						this.showAlert= true;
						this.alertMessage = ""
						if (error.response) {
							this.alertMessage = "Error "+ error.response.status + " " + error.response.data.message;
							// The request was made and the server responded with a status code  that falls out of the range of 2xx
						} else if (error.request) {
							// The request was made but no response was received `error.request` is an instance of XMLHttpRequest in the browser and an instance of
							console.log(error.request);
							this.alertMessage = "Error. Server irrangiungibile."
						} else {
							// Something happened in setting up the request that triggered an Error
							console.log('Error', error.message);
							this.alertMessage = error.message
						}
					});
			},
			
			modify_data_scadenza_patente: function(date){
				//modify a single date of a categoria
				console.log(index);
				// this.nuovaPatente.categorie[index].data_scadenza = 
				return this.customFormatter(date);
			},
			selectData_rilascio_patente: function(data){
				this.nuovaPatente.data_rilascio_patente = this.customFormatter(data);
				this.selectCategoriaRilascio(data);

			},
			selectData_scadenza_patente: function(data){
				this.nuovaPatente.data_scadenza_patente = this.customFormatter(data);
				this.selectCategoriaValidita(data); // data validità categoria uguale alla patente
			},
			sortCategorie:function(){
				this.nuovaPatente.categorie.sort(this._compare);
			},
			_compare: function(a,b){
				// funzione usata per compare e ordinare le categoria
				if (a.categoria < b.categoria) {
					return -1;
				}
				if (a.categoria > b.categoria) {
					return 1;
				}
				// a deve essere uguale a b
				return 0;
			},
			_removeCategoria: function(index){
				this.nuovaPatente.categorie.splice(index, 1);
			},
			open: function(){
				this.showModalAggiungiCategoria = true;
			},
			close: function () {
				this.showModalAggiungiCategoria=false;
			}
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
