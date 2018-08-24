<template id="famiglia-select">
	<form class="form" method="POST">

	<slot name="csrf"></slot>

	<h4>Dati Anagrafici</h4>
	<hr>
	<div class="form-row">
	  <div class="col-md-3">
	    <div class="form-group">
	      <label for="nome">Nome</label>
	      <input type="text" class="form-control" id="nome" placeholder="Nome" name="nome" v-model="nome">
	    </div>
	  </div>
	  <div class="col-md-3">
	    <div class="form-group">
	      <label for="cognome">Cognome</label>
	      <input type="text" class="form-control" id="cognome" name="cognome" placeholder="Cognome" v-model="cognome">
	    </div>
	  </div>
	  <div class="col-md-1">
	    <div class="form-group">
	      <label for="sesso">Sesso</label>
	      <select class="form-control" id="sesso" name="sesso">
	        <option value="" hidden></option>
	        <option value="M">M</option>
	        <option value="F">F</option>
	      </select>
	    </div>
	  </div>
	  <div class="col-md-2">
	    <div class="form-group">
	      <label for="nascita">Data di Nascita</label>
	      <input type="date" class="form-control" id="nascita" name="nascita">
	    </div>
	  </div>
	  <div class="col-md-2">
	    <div class="form-group">
	      <label for="citta">Città di Nascita</label>
	      <input type="text" class="form-control" id="citta" name="citta">
	    </div>
	  </div>
	  <div class="col-md-1">
	    <div class="form-group">
	      <label for="provincia">Provincia</label>
	      <select class="form-control">
	        <slot name="provincie"></slot>
	      </select>
	    </div>
	  </div>
	</div>

	<h4>Dati interni Nomadelfia</h4>
	<hr>
	<!-- Dati interni Nomadelfia -->
	<div class="form-row">
	  <div class="col-md-3">
	    <div class="form-group">
	      <label for="nominativo">Nominativo</label>
	      <input type="text" class="form-control" id="nominativo" name="nominativo" placeholder="Nominativo" data-toggle="tooltip" title="nominativo utilizzato internamente per la persona. es: Matteo L.">
	    </div>
	  </div>

	  <div class="form-group col-md-3">
	      <label for="inputState">Posizione in Nomadelfia</label>
	      <select id="inputState" class="form-control" name="posizione">
	        <option selected hidden value="">Seleziona...</option>
	        <slot name="posizione"></slot>
	      </select>
	  </div>
	  
	  <div class="col-md-2">
	    <div class="form-group">
	      <label for="inizio">Data inizio</label>
	      <input type="date" class="form-control" id="inizio" name="inizio">
	    </div>
	  </div>

	<div class="form-group col-md-2">
	  <label for="gruppo">Gruppo Familiare</label>
	  <select class="form-control" id="gruppo" name="gruppo">
	    <option selected hidden value="">Seleziona...</option>
	    <slot name="gruppi"></slot>
	  </select>
	</div>

	<div class="col-md-2">
	    <div class="form-group">
	      <label for="data_gruppo">Data cambio gruppo</label>
	      <input type="date" class="form-control" id="data_gruppo" name="data_gruppo">
	    </div>
	  </div>
	</div>
	<div class="form-row">
		<div class="form-group col-md-3">
		    <label for="nucleo">Posizione nucleo famigliare</label>
		    <select class="form-control" id="nucleo" name="nucleo" v-model="posizioni_selected">
		      <option selected hidden value="">Seleziona...</option>
		      <option v-for="posizione in posizioni" v-bind:value="posizione.id">{{ posizione.posizione }}</option>

		    </select>
  		</div>
		<div class="form-group col-md-3" v-show="needFamiglia">
			<label for="famiglia">Famiglia di Appartenenza</label>
		    <select id="famiglia" class="form-control" name="famiglia">
		      	<option hidden value="">Seleziona...</option>
		        <option v-for="famiglia in famiglie" v-bind:value="famiglia.id">{{ famiglia.nome }}</option> 
		    </select>
		</div>
	</div>
	<h4>Opzionali</h4>
	<hr>
	<div class="form-row">
	  <div class="form-group col-md-3">
	    <label for="azienda">Azienda</label>
	    <select class="form-control" id="azienda" name="azienda">
	      <option value="" selected>Seleziona...</option>
	      <slot name="aziende"></slot>
	    </select>
	  </div>  

	  <div class="col-md-2">
	    <div class="form-group">
	      <label for="data_lavoro">Inizio lavoro</label>
	      <input type="date" class="form-control" id="data_lavoro" name="data_lavoro">
	    </div>
	  </div>

	  <div class="form-group col-md-3">
	    <label for="incarico">Incarico</label>
	    <select class="form-control" id="incarico" name="incarico">
	      <option value="" selected>Seleziona...</option>
	      <slot name="incarichi"></slot>
	    </select>
	  </div>  
	  
	  <div class="col-md-2">
	    <div class="form-group">
	      <label for="data_incarico">Inizio incarico</label>
	      <input type="date" class="form-control" id="data_incarico" name="data_incarico">
	    </div>
	  </div>

	</div>
	<div class="form-row">
	  <div class="col-md-2 offset-md-10">
	    <button type="submit" class="btn btn-block btn-primary" data-toggle="tooltip" title="Salva">Salva</button>
	  </div>
	</div>
	</form>
</template>

<script>
	export default {
		props: ['api_url', 'old'],
		data(){
			return {
				posizioni_selected: "",
				famiglie: null,
				posizioni: null,
				nome: "",
				cognome: "",
			}
		},
		mounted: function() {
			axios.get("/api/nomadelfia/posizioni").then(response => (this.posizioni = response.data));			
		},
		computed: {
			nomeIntero: function(){
				return this.nome + " " + this.cognome
			},
			needFamiglia: function(){
				if (this.posizioni_selected == ""){
					return true;
				}
				else{
					return !this.posizioni[this.posizioni_selected-1].stato;
				}
			}
		},
		methods: {
			nuovaFamiglia: function() {
				axios.post('/api/nomadelfia/famiglie/create', {
					nome: this.nome_famiglia,
					cognome: this.cognome_famiglia
				})
				.then(function(response) {
					console.log(response);
				})
				.catch(function(error) {
					console.log(error);
				});
			},
			reset: function() {
				this.nome_famiglia = "";
				this.cognome_famiglia = "";
			}
		}
	}
</script>

<style >
	
</style>