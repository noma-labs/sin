<template id="azienda-edit">
	<div>
		<div class="row">
			<div class="col-md-8 table-responsive">
				<table class="table table-hover table-bordered">
					<thead class="thead-inverse">
						<th scope="col" width="40%">Nominativo</th>
						<th scope="col" width="15%" class="text-center">Stato</th>
						<th scope="col" width="20%">Data inizio lavoro</th>
						<th scope="col" width="25%">Operazioni</th>
					</thead>
					<tbody>
						<tr v-for="lavoratore in lavoratori" v-bind:id="lavoratore.id" hoverable>
							<td scope="row">{{ lavoratore.nominativo }}<span v-bind:class="badgeMansione(lavoratore.pivot.mansione)">{{ lavoratore.pivot.mansione }}</span></td>
							<td class="text-center"><span v-bind:class="badgeStato(lavoratore.pivot.stato)">{{ lavoratore.pivot.stato }}</span></td>
							<td>{{ lavoratore.pivot.data_inizio_azienda }}</td>
							<td class="text-center">
								<button class="btn btn-sm btn-warning" v-on:click="modificaLavoratore(lavoratori.indexOf(lavoratore))"><i class="fa fa-edit"></i> Modifica</button>
								<button class="btn btn-sm btn-warning" v-on:click="spostaLavoratore(lavoratori.indexOf(lavoratore))"><i class="fa fa-exchange"></i> Sposta</button>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="row">
					<div class="col-sm-2 offset-sm-10">
						<button class="btn btn-success btn-block" role="button" @click="aggiungiLavoratore">Aggiungi</button>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card">
				  <div class="card-header">
				    Hanno lavorato:
				  </div>
				  <div class="card-body">
				  	<div class="list-group list-group-flush">
				    	<a class="list-group-item list-group-item-secondary d-flex align-items-center list-group-item-action" v-for="storico in lavoratori_storici" v-bind:id="storico.id">{{ storico.nominativo }} <span class="badge badge-danger badge-pill ml-4">{{ storico.pivot.data_fine_azienda }}</span><span class="badge badge-danger badge-pill ml-1">{{ storico.pivot.stato }}</span></a>
					</div>
				  </div>
				</div>
			</div>
		</div>
		<div>
		    <transition name="modal">
		        <div class="modal-mask"  @click="close" v-show="showModal">
		            <div class="modal-container"  @click.stop>
		                <div class="modal-header">
		                    <h3>{{titoloModal}}</h3>
		                </div>
		                <div class="modal-body">
		                	<div class="row">
		                    	<label for="data_inizio" class="col-sm-4">Data inizio lavoro</label>
						   		<div class="col-sm-8">
						   			<date-picker id="data_inizio" @selected="dateInizioSelected" :value="data_inizio_lavoro" :language="language" :format="customFormatter"></date-picker>
						   			<!-- <input type="date" name="data_inizio_lavoro" v-model:value="data_inizio_lavoro"> -->
						   		</div>
							</div>
							<hr>
						    <div class="row">
						   		<label class="col-sm-4">Mansione azienda</label>
						   		<div class="col-sm-8">
									<select class="form-control-sm" v-model:value="mansione">
										<option v-for="man in mansioni">{{man}}</option>
									</select>
						   		</div>
						    </div>
						    <hr>
						    <div class="row">
						   		<label class="col-sm-4">Stato</label>
						   		<div class="col-sm-8">
						   			<div class="form-check form-check-inline" v-for="st in stati">
									  <input class="form-check-input" type="radio" name="stato" :id="st" :value="st" v-bind:checked="stato == st" v-model="stato">
									  <label class="form-check-label" for="st">{{ st }}</label>
									</div>
								</div>
						    </div>
						    <hr v-show="nonAttivoChecked">
						    <div class="row" v-show="nonAttivoChecked">
						   		<label for="data_fine" class="col-sm-4">Data fine lavoro</label>
						   		<div class="col-sm-8">
						   			<date-picker placeholder="Selezionare una data" id="data_fine" @selected="dateFineSelected" :value="data_fine_lavoro" :language="language" :format="customFormatter" :disabledDates="disableDate"></date-picker>
						   		</div>
						    </div>
		                </div>
		                <p class="text-info">{{messageModal}}</p>
		                <div class="modal-footer text-right">
		                	<button class="btn btn-success" role="button" @click="salvaModificheLavoratore" v-bind:disabled="validateData">
		                    	salva
			                </button>
			                <button class="btn btn-danger" role="button" @click="close()">
			                	Esci
			                </button>
		                </div>
		            </div>
		        </div>
		    </transition>
		  </div>

		  <div>
		    <transition name="modal">
		        <div class="modal-mask" @click="closeSposta" v-show="spostaModalShow">
		            <div class="modal-container" @click.stop>
		                <div class="modal-header">
		                    <h3>{{titoloModal}}</h3>
		                </div>
		                <div class="modal-body">
		                	<div class="row">
			                	<div class="col-sm-8">
			                		<div class="row">
			                			<label class="col-sm-4">Azieda attuale:</label>
			                			<div class="col-sm-8">
			                				{{nome}}
			                			</div>
			                		</div>
			                		<hr>
			                		<div class="row">
			                			<label for="data_fine_sposta" class="col-sm-4">Data fine lavoro:</label>
			                			<div class="col-sm-8">
						   					<date-picker placeholder="Selezionare una data" id="data_fine_sposta" :language="language" :format="customFormatter" @selected="dateSpostaSelected" :value="data_fine_sposta" :disabledDates="dateDisabledSposta"></date-picker>
						   				</div>
			                		</div>
			                		<hr>
			                		<div class="row">
				                		<label for="nuova_azienda" class="col-sm-4">Sposta in:</label>
				                		<div class="col-sm-8">
					                		<select id="nuova_azienda" class="form-control-sm" :value="nuova_azienda_id" v-model="nuova_azienda_id" @change="changeAziendaSposta">
					                			<option value="" hidden>Seleziona un'azienda</option>
					                			<option v-for="azienda in aziendePossibili" :value="azienda.id">{{ azienda.nome }}</option>
					                		</select>
				                		</div>
			                		</div>
			                	</div>
			                	<div class="col-sm-4">
			                		<div class="card">
			                			<div class="card-header">Ha lavorato:</div>
			                			<div class="card-body">
			                			<p v-if="aziendeStorico.length == 0" class="text-danger">Nussuna azienda nello storico</p>
										<ul v-else>
											<li v-for="azienda in aziendeStorico">{{azienda.nome}}</li>
										</ul>
										</div>
									</div>
			                	</div>
		                	</div>
		                </div>
		                <div class="row" v-show="!validateSposta">
		                	<div class="col-sm offset-sm-1">
		                		<p class="text-danger">Data inizio lavoro in <strong>{{nome_nuova_azienda}}</strong>: <strong>{{data_fine_sposta}}</strong> </p>
		                	</div>
		                </div>
		                <div class="modal-footer text-right">
		                	<button class="btn btn-success" role="button" :disabled="validateSposta" @click="salvaSpostaLavoratore">
		                    	salva
			                </button>
			                <button class="btn btn-danger" role="button" @click="closeSposta()">
			                	Esci
			                </button>
		                </div>
		            </div>
		        </div>
		    </transition>
		  </div>
		  <div>
		  	<!-- Modal Aggiungi Lavoratore -->
		    <transition name="modal">
		        <div class="modal-mask"  @click="closeAggiungi" v-show="showModalAggiungi">
		            <div class="modal-container"  @click.stop>
		                <div class="modal-header">
		                    <h3>Aggiungi un lavoratore in: {{nome}}</h3>
		                </div>
		                <div class="modal-body">
		                	<div class="row">
		                		<label for="nuovo_lavoratore" class="col-sm-4">Lavoratore:</label>
		                		<div class="col-sm-8">
		                			<v-select
									        :options="persone"
									        @search="searchLavoratore"
									        :placeholder="placeholder"
									        label="nominativo"
									        v-model="nuovo_lavoratore"
								          >
								          <span class="text-danger" slot="no-options">{{noPersone}}</span>
								    </v-select>
		                		</div>
		                	</div>
		                	<hr>
		                	<div class="row">
	                			<label for="inizio_lavoratore" class="col-sm-4">Data inizio lavoro:</label>
	                			<div class="col-sm-4"> 
	                				<date-picker placeholder="Selezionare una data" :language="language" :format="customFormatter" @selected="dateAggiungiSelected"></date-picker>
	                			</div>
		                	</div>
		                </div>
		                <p class="text-info"></p>
		                <div class="modal-footer text-right">
		                	<button class="btn btn-success" :disabled="validateAggiungi" @click="salvaNuovoLavoratore">
		                    	salva
			                </button>
			                <button class="btn btn-danger" @click="closeAggiungi">
			                	Esci
			                </button>
		                </div>
		            </div>
		        </div>
		    </transition>
		  </div>
	</div>
</template>

<script>
	import {it} from 'vuejs-datepicker/dist/locale'
	import vSelect from "vue-select"

	export default {
		components: {vSelect},
		props: ['url_azienda_edit', 'url_mansioni', 'url_stati', 'url_modifica_lavoratore', 'id_azienda'],
		data: function() {
			return {
				language: it,
				nome: null,
				lavoratori: null,
				lavoratori_storici: null,
				tipo: null,
		        showModal:false,
		        mansioni: null,
		        stati: null,
		        titoloModal: '',
		        messageModal: '',
		        data_inizio_lavoro: '',
		        lavoratore_id: '',
		        mansione: '',
		        stato: '',
		        data_fine_lavoro: '',
		        // data for modal spostaLavoratore
		        spostaModalShow: false,
		        aziendePossibili: {},
		        aziendeStorico: {},
		        nuova_azienda_id: '',
		        data_fine_sposta: '',
		        dateDisabledSposta: {},
		        nome_nuova_azienda: '',
		        url_sposta: "/api/nomadelfia/azienda/sposta/lavoratore",
		        // data for modal aggiungiLavoratore
		        showModalAggiungi: false,
		        nuovo_lavoratore: '',
		        url_search: '/api/nomadelfia/azienda/aggiungi/search',
		        url_aggiungi: '/api/nomadelfia/azienda/aggiungi/lavoratore',
		        persone: [],
		        placeholder: 'Ricerca la persona...',
		        nuovo_data_inizio: '',
			};
		},
		created:async function(){
			await axios.get(this.url_azienda_edit).then(response => (this.nome = response.data[0].nome, this.lavoratori = response.data[0].lavoratori, this.tipo = response.data[0].tipo, this.lavoratori_storici = response.data[0].lavoratoriStorici));
			await axios.get(this.url_mansioni).then(response => (this.mansioni = response.data));
			await axios.get(this.url_stati).then(response => (this.stati = response.data));
		},
		computed:{
			noPersone: function(){
				return this.persone.length == 0 ? "nessuna persona trovata" : "";
			},
			validateAggiungi: function(){
				return this.nuovo_data_inizio == '' || this.nuovo_lavoratore == '';
			},
			nonAttivoChecked: function(){
				return this.stato == 'Non Attivo'; 
			},
			validateData: function(){
				return this.data_fine_lavoro == '' && this.stato == 'Non Attivo';
			},
			disableDate: function(){
				if(this.data_inizio_lavoro != ''){
					var fine = new Date(this.data_inizio_lavoro);
					fine.setDate(fine.getDate() + 1)
			    	return { 
			    		to: fine
			    	}
			    }
			    else{
			    	return {}
			    }
		    },
		    validateSposta: function(){
		    	return this.nuova_azienda_id == '' || this.data_fine_sposta == '';
		    }
		},
		methods:{
			changeAziendaSposta: function(){
				this.nome_nuova_azienda = this.aziendePossibili[this.nuova_azienda_id].nome;
			},
			aggiungiLavoratore: function(){
				this.showModalAggiungi = true;
			},
			modificaLavoratore: function(id){
				this.showModal = true;
				this.titoloModal = 'Modifica lavoratore: '+this.lavoratori[id].nominativo;
				this.data_inizio_lavoro = this.lavoratori[id].pivot.data_inizio_azienda;
				this.mansione = this.lavoratori[id].pivot.mansione;
				this.stato = this.lavoratori[id].pivot.stato;
				this.lavoratore_id = this.lavoratori[id].id;
			},
			spostaLavoratore: async function(id){
				const response = await axios.get("/api/nomadelfia/aziende/lavoratore/"+this.lavoratori[id].id+'?filtro=storico');
				this.aziendeStorico = response.data;
				await axios.get("/api/nomadelfia/aziende/lavoratore/"+this.lavoratori[id].id+'?filtro=possibili').then(response => (this.aziendePossibili = response.data));
				this.titoloModal = 'Sposta lavoratore: '+this.lavoratori[id].nominativo;
				this.disableDateSposta(this.lavoratori[id].pivot.data_inizio_azienda);
				this.spostaModalShow = true;
				this.lavoratore_id = this.lavoratori[id].id;
			},
			isDisabled: function(){
	        	return  !this.nome;
	      	},
	      	close: function () {
		        this.msg = null,
		        this.titoloModal = '',
		        this.messageModal = '',
		        this.lavoratore_id = '',
		        this.data_inizio_lavoro = '',
		        this.mansione = '',
		        this.stato = '',
		        this.showModal=false,
		        this.data_fine_lavoro = ''
		    },
		    closeSposta: function () {
		        this.spostaModalShow = false,
		        this.titoloModal = '',
		        this.aziendePossibili = {},
		        this.aziendeStorico = {},
		        this.data_fine_sposta = '',
		        this.nuova_azienda_id = '',
		        this.dateDisabledSposta = {},
		        this.nome_nuova_azienda = '',
		        this.lavoratore_id = ''
		    },
		    closeAggiungi: function() {
		    	this.showModalAggiungi = false;
		    	this.persone = [];
		    	this.nuovo_lavoratore = '';
		    	this.nuovo_data_inizio = '';
		    },
		    badgeMansione: function(mansione){
				return {
					badge: true,
					'badge-success': mansione == 'RESPONSABILE AZIENDA',
					'badge-warning': mansione != 'RESPONSABILE AZIENDA',
					'badge-pill': true,
					'ml-3': true
				}
			},
			badgeStato: function(stato){
				return {
					badge: true,
					'badge-success': stato == 'Attivo',
					'badge-warning': stato != 'Attivo',
					'badge-pill': true,
					'ml-3': true
				}
			},
			salvaModificheLavoratore: function(){
				axios.post(this.url_modifica_lavoratore, {
				    mansione: this.mansione,
				    stato: this.stato,
				    data_inizio: this.data_inizio_lavoro,
				    azienda_id: this.id_azienda,
				    lavoratore_id: this.lavoratore_id,
				    data_fine: this.data_fine_lavoro
				  })
				  .then(function (response) {
				    location.reload();
				  })
				  .catch(function (error) {
				    console.log(error);
				  });
			},
			salvaSpostaLavoratore: function(){
				axios.post(this.url_sposta, {
					id_lavoratore: this.lavoratore_id,
					data: this.data_fine_sposta,
					nuova_azienda_id: this.nuova_azienda_id,
					id_azienda: this.id_azienda
				})
				.then(function (response) {
				    location.reload();
				  })
				  .catch(function (error) {
				    console.log(error);
				  });
			},
			customFormatter(date) {
		      return moment(date).format('YYYY-MM-DD');
		    },
		    dateInizioSelected: function(date){
		    	this.data_inizio_lavoro = moment(date).format('YYYY-MM-DD');
		    },
		    dateFineSelected: function(date){
		    	this.data_fine_lavoro = moment(date).format('YYYY-MM-DD');
		    },
		    dateSpostaSelected: function(date){
		    	this.data_fine_sposta = moment(date).format('YYYY-MM-DD');
		    },
		    dateAggiungiSelected: function(date){
		    	this.nuovo_data_inizio = moment(date).format('YYYY-MM-DD');
		    },
		    disableDateSposta: function(date){
		    	var fine = new Date(date);
				fine.setDate(fine.getDate() + 1)
		    	this.dateDisabledSposta = {
		    		to: fine
		    	}
		    },
		    searchLavoratore: function(search, loading) {
		    	loading(true);
		    	axios.get(this.url_search+'?term='+search+'&azienda_id='+Number(this.id_azienda))
		    	.then(
		    		response => (this.persone = response.data),
		    		loading(false)
		    	);
		    },
		    salvaNuovoLavoratore: function() {
		    	axios.post(this.url_aggiungi, {
		    		azienda_id: this.id_azienda,
		    		lavoratore_id: this.nuovo_lavoratore.id,
		    		data: this.nuovo_data_inizio
		    	}).then(function(){
		    		location.reload();
		    	});
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