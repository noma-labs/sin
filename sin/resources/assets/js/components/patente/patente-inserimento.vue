<template id="categorie">
	<div>
		<div class="alert"  v-show="showAlert" v-bind:class="[hasError ? 'alert-danger' : '', 'alert-success']" role="alert"> 
			{{alertMessage}} </strong>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
						<label for="numero_patente">Persona:</label>
						<v-select
							:options="optionsPersone"
							:debounce="500"
							:on-search="getPersonePatenti"
							:on-change="changedPersonaSelected"
							:placeholder="personaPlaceholder"
							:clearable=true
							:disabled="disabledAll"
							:label="label">
							<span slot="no-options">Nessuna persona trovata</span>
						</v-select>
					</div>

					<!-- <div class="col-md-6">
						<label for="nome_cognome">Nome Cognome:</label>
						<input type="text" class="form-control" v-bind:value="personaNomeCognome" disabled>
					</div> -->
				</div> <!-- end zero row in left colum-->
				<div class="row">
					<div class="col-md-6">
						<label for="data_nascita">Data di nascita:</label>
						<input type="text" class="form-control" v-bind:value="personaSelected.data_nascita" disabled>
					</div>
					<div class="col-md-6">
						<label for="luogo_nascita">Luogo di nascita:</label>
						<input type="text" class="form-control" v-bind:value="personaSelected.provincia_nascita" disabled>
					</div>
				</div><!-- end first row in left colum-->
				<div class="row">
					<div class="col-md-6">
						<label for="data_rilascio_patente">Patente rilasciata il:</label>
						<date-picker name="data_rilascio_patente" 
									@selected="selectData_rilascio_patente" 
									:bootstrap-styling="true" 
									:language="language" 
									:format="customFormatter"
									:disabled="disabledAll">
						</date-picker>
					</div>
					
					<div class="col-md-6">
						<label for="rilasciata_dal">Rilasciata dal:</label>
						<input type="text" class="form-control"  
								v-model="nuovaPatente.rilasciata_dal" 
								id="rilasciata_dal" 
								name="rilasciata_dal" 
								:disabled=disabledAll>
					</div>
				</div><!-- end second row in left colum-->
				<div class="row">
					<div class="col-md-6">
							<label for="data_scadenza_patente">Patente valida fino al:</label>

							<date-picker  name="data_scadenza_patente" 
										@selected="selectData_scadenza_patente"
										:bootstrap-styling="true" 
										:language="language" 
										:disabledDates="disabledData_scadenza_patente"
										:format="customFormatter"
										:disabled="disabledAll">
							</date-picker>
						</div>
					<div class="col-md-6">
						<label for="numero_patente">Numero Patente:</label>
						<input type="text" class="form-control"  autocomplete="off" v-model="nuovaPatente.numero_patente" name="numero_patente" :disabled=disabledAll>
					</div>
				</div><!-- end third row in left colum-->
				<div class="row">
					<div class="form-group col-md-12">
						<label for="note">Note:</label>
						<textarea class="form-control" v-model="nuovaPatente.note" name="note" :disabled=disabledAll></textarea>
					</div>
				</div> <!-- end fouth row in left colum-->
				<div class="row">
					<div class="col-md-4">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="assegnaCommissione"  true-value="commissione" false-value="null" v-model="nuovaPatente.stato" id="ycommissione" >
							<label class="form-check-label" for="ycommissione">
								Con  commissione.
							</label>
						</div>
					</div>
					<div class="col-md-3 offset-md-8">
						<!-- <div>&nbsp;</div> -->
						<button type="submit" 
								@click="salvaNuovaPatente"
								:disabled="disabledSalvaNuovaPatente || disabledAll" 
								class="btn btn-primary">
								Salva
						</button>
					</div>
					<!-- disabledSalvaNuovaPatente || disabledAll -->

				</div> <!-- end fifth row in left colum-->
				<!--end sixth row in left column-->					
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
										<b>{{ cqc.categoria }}</b>  <span class="badge badge-success">{{ cqc.data_rilascio }} </span> <span class="badge badge-danger">{{ cqc.data_scadenza }}</span> 
									</div>
								<button class="btn btn-warning mt-3" @click="openAggiungiCQC" :disabled="!areCheckedCD()" >Aggiungi</button>
							</div>
						</div>
					</div>
			 	</div> 	<!-- end row categorie -->
	
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
									<input class="form-check-input" type="checkbox" :value="categoria" v-model="nuovaPatente.categorie" id="defaultCheck1">
									<label class="form-check-label" for="defaultCheck1">
											{{ categoria.categoria }} 
									</label>
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
			<div class="modal-mask"  v-show="showModalAggiungiCQC">
				<div class="modal-container"  @click.stop>
					<div class="modal-header">
						<h3>Aggiungi CQC</h3>
					</div>
					<div class="modal-body">	
						<div class="row" v-for="(cqc, index) in cqcPossibili">
							<div class="col-md-3" >
								<div class="form-check">
									<input class="form-check-input" type="checkbox" :value="cqc" v-model="nuovaPatente.cqc" id="defaultCheck1">
									<label class="form-check-label" for="defaultCheck1">
											{{ cqc.categoria }} 
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<label>Rilasciata il:</label>
								<date-picker :bootstrap-styling="true" 
										 	v-model="cqc.data_rilascio"
											placeholder="---Seleziona una data---" 
											:language="language" 
											:format="customFormatter"
											>
								</date-picker>
							</div>
							<div class="col-md-4">
								<label>Valida fino al:</label>
								<date-picker :bootstrap-styling="true" 
											v-model="cqc.data_scadenza"
											placeholder="---Seleziona una data---" 
											:language="language" 
											:format="customFormatter"
											>
								</date-picker>
							</div>
						</div>  <!-- end row C.Q.C -->
					</div>
				    <div class="modal-footer text-right">
						<button class="btn btn-danger" @click="salvaSelectedCqc" :disabled="areNullDateCQC">OK</button>
					</div>
				</div>
			</div>
		</transition>

		<!-- Modal patente aggiunta -->
		<transition name="modalPatente">
			<div class="modal-mask"  @click="closeModalPatenteAggiunta" v-show="showModalPatenteAggiunta">
				<div class="modal-container"  @click.stop>
					<div class="modal-header">
						<h3> Inserimento patente </h3>
					</div>
					<div class="modal-body">
						{{alertMessage}}
					</div>
					<div class="modal-footer text-right">
						<a v-bind:href="modalPatenteAggiunta.urlInserimento" class="btn btn-success" role="button" aria-pressed="true">Aggiungi altra patente</a>
						<a v-bind:href="modalPatenteAggiunta.urlPatente" class="btn btn-warning" role="button" aria-pressed="true">Visualizza patente inserita</a>
						<button class="btn btn-danger" @click="closeModalPatenteAggiunta" >Esci</button>
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
			'apiPatentePersone',
			'apiPatenteCategorie',
			'apiPatenteCqc',
			'apiPatenteCreate'
			],
		data: function() {
			return {
				label: "value", 			// label to shown in the input field nella "ricerca persona"
				disabledAll: false, 			// usato per disabilitàre  gli elementi.
				showAlert: false,  			    // if true the alert is shown 
				hasError : false, 		     	// if hasErroe=true the text-success alert message is shown 
				alertMessage: "",				// message to be shown in the alert
				optionsPersone: [],             // list of the persone to be shown in the dropdown menu
				personaSelected: {	         	// persona selezionata dal dropdown menu
					persona_id : null,
					nome:null,
					cognome: null,
					data_nascita: null,
					provincia_nascita :null
				},						
				personaPlaceholder: "Inserisci nome o cognome della persona...",
				language: it,
				showModalAggiungiCategoria: false,  	// if true modal dell'aggiunta della categoria viene visualizzto
				showModalAggiungiCQC: false,  			// if true modal dell'aggiunta del C.Q.C viene visualizzato
				showModalPatenteAggiunta: false,    // if true modal is shown
				modalPatenteAggiunta : {				// url mostrati nel modal di conferma aggiunta nuova patente
					urlInserimento : null,
					urlPatente: null
				},
				categoriePossibili: [],             // categorie disponibili da assegnare alla patente
				cqcPossibili : [], 					// cqc possibili (persona e merci)
				nuovaPatente : {
					persona_id : null,
					data_nascita : null,
					luogo_nascita :null,
					data_rilascio_patente : null,
					data_scadenza_patente : null,
					rilasciata_dal :null,
					numero_patente: null,
					stato: "null",                // 'commissione': la patente è stata associata alla commissione, NULL otherwise
					note : null,	      	
					categorie:[],				 // array delle nuove categorie assegnate alla patente
					cqc: []                     // array dei c.q.c assegnati alla patente
				},
			};
		},
		created: function(){
			axios.get(this.apiPatenteCategorie).then(response => {
				 this.categoriePossibili = response.data;
			});
			axios.get(this.apiPatenteCqc).then(response =>{
				this.cqcPossibili = response.data;
				this.cqcPossibili.forEach(cqc => { // aggiunge la data_rilascio e data_scadenza agli oggetti cqc.
					cqc.data_rilascio = null;
					cqc.data_scadenza = null;

				})
			});
		},
		computed:{
			areNullDateCQC : function(){
				// true if there are some C.Q.C date equal to null
				// var res = _.some(this.nuovaPatente.cqc, { 'data_rilascio': null})  || _.some(this.nuovaPatente.cqc,  {'data_scadenza': null });
				// console.log("data rilascio: ")
				// console.log(_.some(this.nuovaPatente.cqc, { 'data_rilascio': null}))
				// console.log("data scadenza null: ")
				// console.log(_.some(this.nuovaPatente.cqc,  {'data_scadenza': null }))
				// return res;
				return false;
			},
			disabledSalvaNuovaPatente: function(){
				// return this.areNullDateCQC
				return    this.nuovaPatente.persona_id === null
				 		|| this.nuovaPatente.numero_patente === null
						|| this.nuovaPatente.data_rilascio_patente === null
						|| this.nuovaPatente.data_scadenza_patente === null
			},
			disabledData_scadenza_patente: function(){
				var data = this.transformIntoDate(this.nuovaPatente.data_rilascio_patente,"YYYY-MM-DD");
				return {
					to: data
				}
			},
		},
		methods:{
			formatCQCDate(){
				this.cqcPossibili.forEach(cqc => { // aggiunge la data_rilascio e data_scadenza agli oggetti cqc.
					cqc.data_rilascio = this.customFormatter(cqc.data_rilascio);
					cqc.data_scadenza = this.customFormatter(cqc.data_scadenza);

				})
			},
			// ritorna true se le categoria C o D sono selezionate, altrimento false
			areCheckedCD(){
				return _.some(this.nuovaPatente.categorie, {categoria:"C"}) ||  
					  _.some(this.nuovaPatente.categorie, {categoria:"D"}) 
			},
			getCategoriaFromID: function(id){
				return _.find(this.categoriePossibili, { 'id':id }).categoria;
			},
			changedPersonaSelected: function(persona){
				if(persona){
					this.nuovaPatente.persona_id  = persona.persona_id;
					this.personaSelected = persona;
				 }
				else
					this.resetPersona()
			},
			getPersonePatenti: function(search, loading){
				loading = true;
				axios.get(this.apiPatentePersone, { params: { term: search }})
					.then(response => {
					this.optionsPersone = response.data;
					loading = false;
				});
			},
			getCategoriePossibili(){
				axios.get(this.apiPatenteCategorie).then(response => {
					this.categoriePossibili = response.data;
				});
			},
			salvaNuovaPatente(){
				axios.post(this.apiPatenteCreate, this.nuovaPatente)
					.then(response=>{
						this.showAlert = true;
						this.alertMessage = response.data.msg;
						if (response.data.err === 0){ // there is no error
							this.hasError=false;
							this.disabledAll = true;
							this.modalPatenteAggiunta = response.data.data;
							this.openModalPatenteAggiunta();
						}
						else
							this.hasError=true;
					})
					.catch(error => {
						this.hasError= true;
						this.showAlert= true;
						this.alertMessage = ""
						if (error.response) {
							this.alertMessage = "Error "+ error.response.status + " " + error.response.data.message;
						} else if (error.request) {
							this.alertMessage = "Error. Risposta non ricevuta dal server"
						} else { // Something happened in setting up the request that triggered an Error
							console.log('Error', error.message);
							this.alertMessage = error.message
						}
					});
			},
			salvaSelectedCqc: function(){
				this.closeModalAggiungiCqc();
			},
			customFormatter(date) {
			  //return moment(date).format('DD-MM-YYYY'); //,"YYYY-MM-DD"
		      return moment(date).format('YYYY-MM-DD'); //,"YYYY-MM-DD"
			  
			},
			transformIntoDate: function(data, format){
				var date = moment(data, format);
				return new Date(date.year(), date.month(),date.date()) // month is from [0-11]
			},
			selectData_rilascio_patente: function(data){
				this.nuovaPatente.data_rilascio_patente = this.customFormatter(data);
			},
			selectData_scadenza_patente: function(data){
				this.nuovaPatente.data_scadenza_patente = this.customFormatter(data);
				this.selectCategoriaValidita(data);
				//this.nuovaCategoria.data_scadenza = this.transformIntoDate(this.nuovaPatente.data_scadenza_patente,'DD-MM-YYYY') //this.loadFormattedDate(this.nuovaPatente.data_scadenza_patente);
			},
			sortCategorie:function(){
				this.nuovaPatente.categorie.sort(this._compare);
			},
			// funzione usata per ordinare due categoria
			_compare: function(a,b){
				if (a.categoria < b.categoria) {
					return -1;
				}
				if (a.categoria> b.categoria) {
					return 1;
				}
				// a deve essere uguale a b
				return 0;
			},
			openAggiungiCategoria: function(){
				this.showModalAggiungiCategoria = true;
				this.getCategoriePossibili();
			},
			openAggiungiCQC: function(){
				this.showModalAggiungiCQC = true;
			},
			openModalPatenteAggiunta: function(){
				this.showModalPatenteAggiunta = true;
			},
			close: function () {
				this.showModalAggiungiCategoria=false;
				this.sortCategorie();
				this.reset();
			},
			closeModalAggiungiCqc : function () {
				this.showModalAggiungiCQC=false;
				this.formatCQCDate();
			},
			closeModalPatenteAggiunta:function(){
				this.showModalPatenteAggiunta = false;
				this.reset();
			},
			reset: function(){
				this.modalPatenteAggiunta.urlInserimento = null;
				this.modalPatenteAggiunta.urlPatente = null;
			},
			resetPersona: function(){
					this.personaSelected.nome = null;
					this.personaSelected.persona_id = null;
					this.personaSelected.cognome = null;
					this.personaSelected.data_nascita = null;
					this.personaSelected.provincia_nascita = null;
					this.nuovaPatente.persona_id = null;
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