<template id="categorie">
	<div>
		<div class="alert "  v-show="showAlert" v-bind:class="[hasError ? 'alert-danger' : '', 'alert-success']" role="alert"> 
			{{alertMessage}} </strong>
			<!-- <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> -->
		</div>

		<div class="row">
			<div class="col-md-6">

				<slot name="persona-info"></slot>

				<div class="row">
					<div class="col-md-6">
						<label for="data_rilascio_patente">Patente rilasciata il:</label>
						<date-picker name="data_rilascio_patente" 
									@selected="selectData_rilascio_patente" 
									:bootstrap-styling="true" 
									:language="language" 
									:format="customFormatter"
									v-model="nuovaPatente.data_rilascio_patente"
									:disabled="disabledAll">
						</date-picker>
					</div>
					
					<div class="col-md-6">
						<label for="rilasciata_dal">Rilascia da:</label>
						<input type="text" class="form-control"  
											v-model="nuovaPatente.rilasciata_dal" id="rilasciata_dal" 
											name="rilasciata_dal" 
											:disabled=disabledAll>
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
										v-model="nuovaPatente.data_scadenza_patente"
										:disabled="disabledAll">
							</date-picker>
						</div>
					<div class="col-md-6">
						<label for="numero_patente">Numero Patente:</label>
						<input type="text" class="form-control" v-model="nuovaPatente.numero_patente" name="numero_patente" :disabled=disabledAll>
					</div>
				</div><!-- end third row in left colum-->
				<div class="row">
					<div class="form-group col-md-9">
						<label for="note">Note:</label>
						<textarea class="form-control" v-model="nuovaPatente.note" name="note" :disabled=disabledAll></textarea>
					</div>
					<div class="form-group col-md-3 m-*-auto">
					 <div>&nbsp;</div>
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
				<div class="row" v-if="nuovaPatente.categorie.length">
						<div class="col-md-4">Categoria</div>
						<div class="col-md-4">Data rilascio</div>
						<div class="col-md-4">Data scadenza</div>
						<!-- <div class="col-md-3">Restrizioni</div> -->
				</div>
				<div class="row" v-for="categoria in nuovaPatente.categorie">
					<div class="col-md-4">
						{{categoria.categoria}}	
						<!-- {{categoria.categoria.categoria}}	 -->
					</div>
					<div class="col-md-4">
						<date-picker 
							v-model="categoria.pivot.data_rilascio" 
							placeholder="---Seleziona una data---" 
							:language="language" 
							:format="customFormatter"
							:disabled=disabledAll>
						</date-picker>
					</div>
					<div class="col-md-4">
						<date-picker v-model="categoria.pivot.data_scadenza" 
									placeholder="---Seleziona una data---" 
									:language="language" 
									:format="customFormatter"
									:disabled=disabledAll>
						</date-picker>
					</div>
					<!-- <div class="col-md-3">
						{{categoria.restrizione}}	
					</div> -->
				</div>
				<div class="row">
					<button class="btn btn-warning col-md-4 offset-md-8" @click="open" :disabled=disabledAll>Aggiungi categoria</button>
				</div>
			 </div>  <!-- end  rigth column -->
		</div> <!-- end first row -->
		
		<!-- Modal Aggiungi Lavoratore -->
		    <transition name="modal">
		        <div class="modal-mask"  @click="close" v-show="showModalAggiungiCategoria">
		            <div class="modal-container"  @click.stop>
		                <div class="modal-header">
		                    <h3>Aggiungi Categoria</h3>
		                </div>
		                <div class="modal-body">
							<div class="form-group ">
									<label>Categoria</label>
									<!-- v-model="nuovaCategoria"  -->
									<select class="form-control"  v-model="nuovaCategoria.categoria">
										<option :selected="true" >---Selezione categoria---</option>
										<option v-for="categoria in categoriePossibili" v-bind:value="categoria">
											{{ categoria.categoria }} - {{categoria.descrizione}}
										</option>
									</select>
							</div>
							<div class="row">
								<div class="col-md-6">
									<label>Categoria rilasciata il:</label>
									<date-picker  :bootstrap-styling="true" 
												placeholder="Selezionare una data" 
												:value="nuovaCategoria.data_rilascio"
												@selected="selectCategoriaRilascio"
												:language="language" 
												:format="customFormatter"> 
									</date-picker>
								</div>
								<div class="col-md-6">
									<label>Categoria valida fino al:</label>
									<date-picker :bootstrap-styling="true" 
												placeholder="Selezionare una data" 
												:value="nuovaCategoria.data_scadenza"
												@selected="selectCategoriaValidita" 
												:disabledDates="disabled_data_scadenza_categoria"
												:language="language" 
												:format="customFormatter"> 
											
									</date-picker>
								</div>
							</div>

							<div class="form-group">
								<label lass="form-control">Restrizione</label>
								<input type="input" class="form-control" v-model="nuovaCategoria.restrizioni" >
							</div>
						</div>
		         <div class="modal-footer text-right">
							<input class="btn btn-success" type="button" :disabled="disabledSalvaNuovaCategoria" @click="salvaAggiungiCategoria" value="Salva">
							<a class="btn btn-danger" href="#" role="button" @click="close">Chiudi</a>
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
			'apiPatenteCreate'
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
					data_nascita_persona: "",
					provincia_nascita :""
				},						
				personaPlaceholder: "---inserisci nominativo---",
				language: it,
				showModalAggiungiCategoria: false,  // if true modal is shown
				categoriePossibili: [],             // categorie disponibili da assegnare alla patente
				nuovaPatente : {
					data_rilascio_patente : null,
					data_scadenza_patente : null,
					rilasciata_dal :null,
					numero_patente: null,
					note : null,
					categorie: [
						// categoria:"D E"
						// descrizione:"CONSEGUIBILE A 24 ANNI ( CON L'OBBLIGO DI AVER CONSEGUITO LA PATENTE DI CATEGORIA D)"
						// id:14
						// note:""
						// pivot:Object
							// categoria_patente_id:14
							// data_rilascio:"2016-01-07"
							// data_scadenza:"2021-01-05"
							// numero_patente:"U
					],          	// array delle nuove categorie assegnate alla patente
				},
				nuovaCategoria: {
					categoria : {
						id: -1, 
						categoria: null,
						note: null
					},
					data_rilascio: null, 
					data_scadenza: null,
					restrizioni : null
				}, 
			};
		},
		created: function(){
			axios.get(this.apiPatente).then(response => {
				console.log(response.data);
				this.nuovaPatente = response.data;
				// {persona:
				//  categoria_id: 1
				//  data_nascita_persona: "1949-02-01"
				//  datipersonali:
				//		  cognome: "GRAZIOLI"
				//		  data_nascita: "1949-02-01"
				//		  nome: "ADELELMO"
				//		  persona_id: 2
				//		  provincia_nascita: "GHEDI"
				//		  sesso: "M"
				// 	data_rilascio_patente: "2018-10-01"
				// 	data_scadenza_patente: "2018-10-15"
				// 	note: null
				// 	numero_patente: "GR-adelelemo"
				// 	persona_id: 2
				// 	rilasciata_dal: "frosseto"
				// 	stato: "commissione"
				// 	categorie: [
				// 	 {id: 0, 
				// 		categoria: "BS", 
				// 		descrizione: "SPECIALE", 
				// 		note: "", 
				// 		pivot: {
				// 			categoria_patente_id: 0
				// 			data_rilascio: "2018-10-03"
				// 			data_scadenza: "2018-10-18"
				// 			numero_patente: "GR-adelelemo"
				// 			restrizione_codice: null
				// 	}]
				// }
			});
		},
		computed:{
			disabledSalvaNuovaCategoria: function(){
				return  this.nuovaCategoria.categoria == null 
				      ||  this.nuovaCategoria.categoria.id == -1
					  || this.nuovaCategoria.data_rilascio == null 
					  || this.nuovaCategoria.data_scadenza == null 			
			},
			disabledSalvaNuovaPatente: function(){
				// return  this.nuovaPatente.categorie === null 
				// 		|| this.nuovaPatente.categorie === [] 
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
			customFormatter(date) {
		      return moment(date).format('YYYY-MM-DD');
			},
			getCategoriePossibili(){
				axios.get(this.apiPatenteCategorie).then(response => {
					this.categoriePossibili = response.data;
				});
				// var api = "/api/patente/categorie";
				// if(this.numero_patente != null)
				//    api = "/api/patente/"+this.numero_patente+"/categorie?filtro=possibili";
				   
				// axios.get(api).then(response => {
				// 	this.categoriePossibili = response.data;
				// 	console.log(this.categoriePossibili);
				// });
			},
			salvaNuovaPatente(){
				axios.post(this.apiPatenteCreate, this.nuovaPatente)
					.then(response=>{
						this.showAlert= true;
						this.alertMessage = response.data.msg;
						if (response.data.err === 0){
							this.hasError=false;
							this.disabledAll = true;
						}
						else
							this.hasError= true;
	
						// {"err":0,"msg":"patente inserita correttamente"}
						// {"err":1,"msg":"patente inserita correttamente"}
					})
					.catch(error => {
						this.hasError= true;
						this.showAlert= true;
						this.alertMessage = ""
						if (error.response) {
							this.alertMessage = "Error "+ error.response.status + " " + error.response.data.message;
							// The request was made and the server responded with a status code  that falls out of the range of 2xx
							// console.log(error.response.data);
							// console.log(error.response.status);
							// console.log(error.response.headers);
						} else if (error.request) {
							// The request was made but no response was received `error.request` is an instance of XMLHttpRequest in the browser and an instance of
							console.log(error.request);
							this.alertMessage = "Error. Risposta non ricevuta dal server"
						} else {
							// Something happened in setting up the request that triggered an Error
							console.log('Error', error.message);
							this.alertMessage = error.message
						}
					});
			},
			
			selectCategoriaValidita: function(data){
				this.nuovaCategoria.data_scadenza = this.customFormatter(data);
			},
			selectCategoriaRilascio: function(data){
				this.nuovaCategoria.data_rilascio = this.customFormatter(data);
			},
			selectData_rilascio_patente: function(data){
				this.nuovaPatente.data_rilascio_patente = this.customFormatter(data);
				this.selectCategoriaRilascio(data);

			},
			selectData_scadenza_patente: function(data){
				this.nuovaPatente.data_scadenza_patente = this.customFormatter(data);
				this.selectCategoriaValidita(data); // data validità categoria uguale alla patente
			},
			salvaAggiungiCategoria : function(){
				// aggiunge la nuova categoria nella liste delle categorie assegnate alla patente
				// this.nuovaPatente.categorie.push(Object.assign({},this.nuovaCategoria));
				this.nuovaPatente.categorie.push({categoria: this.nuovaCategoria.categoria.categoria, 
												id: this.nuovaCategoria.categoria.id,
												pivot:{ 
													data_rilascio:  this.nuovaCategoria.data_rilascio,
													data_scadenza:  this.nuovaCategoria.data_scadenza
													}
												});
				this.close();
			},
			open: function(){
				this.showModalAggiungiCategoria = true;
				this.getCategoriePossibili();
			},
			close: function () {
				this.showModalAggiungiCategoria=false;
				this.reset();
			},
			reset: function(){
				this.nuovaCategoria.categoria = {
						id: -1, 
						categoria: null,
					},
				this.nuovaCategoria.restrizioni = null;
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