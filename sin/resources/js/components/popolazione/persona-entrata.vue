<template>
<div>
       <div class="form-group row">
            <label for="fornascita" class="col-sm-6">Nome</label>
            <div class="col-sm-6">
                <p class="font-weight-bold">{{this.persona.nominativo}}</p>
            </div>
        </div>
        <div class="form-group row">
            <label for="fornascita" class="col-sm-6">Data nascita</label>
            <div class="col-sm-6">
                <p class="font-weight-bold">{{this.persona.data_nascita}}</p>
            </div>
        </div>

        <h5> Come è entrato in Nomadelfia?</h5>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="tipologia" id="dallaNascita" value="dalla_nascita" v-model="tipologiaEntrata">
          <label class="form-check-label" for="dallaNascita">
            Nato a Nomadelfia
          </label>
        </div>

        <div class="form-check">
          <input class="form-check-input" type="radio" name="tipologia" id="dallaNascita" value="minorenne_accolto" v-model="tipologiaEntrata">
          <label class="form-check-label" for="dallaNascita">
            Minorenne accolto 
          </label>
        </div>

        <div class="form-check">
          <input class="form-check-input" type="radio" name="tipologia" id="dallaNascita" value="minorenne_famiglia" v-model="tipologiaEntrata">
          <label class="form-check-label" for="dallaNascita">
            Minorenne entrato/a con la famiglia 
          </label>
        </div>

        <div class="form-check">
          <input class="form-check-input" type="radio" name="tipologia" id="dallaNascita" value="maggiorenne_single" v-model="tipologiaEntrata">
          <label class="form-check-label" for="dallaNascita">
            Maggiorenne single
          </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tipologia" id="dallaNascita" value="maggiorenne_famiglia" v-model="tipologiaEntrata">
            <label class="form-check-label" for="dallaNascita">
              Maggiorenne con famiglia
            </label>
        </div>

        <br>
        
        <div v-if="tipologiaEntrata" class="form-group row">
            <label class="col-sm-6 col-form-label" for="dallaNascita">Data Entrata: </label>
            <div class="col-sm-6">
                <date-picker name="data_entrata" 
                    @selected="selectDataEntrata" 
                    :bootstrap-styling="true" 
                    :language="it" 
                    :format="customFormatter"
                    :disabled="isNatoANomadelfia ? true: false"
                    :value="isNatoANomadelfia ? persona.data_nascita: null"
                    >
                </date-picker>
            </div>
        </div>
        
        <div v-if="isNatoANomadelfia || isMinorenneAccolto || isMinorenneConFamiglia" class="form-group row">
            <label  class="col-form-label col-sm-6" for="famiglia">Famiglia:</label>
            <div class="col-sm-6">
                <autocomplete placeholder="Inserisci famiglia..." name="famiglia_id" :url="this.apiNomadelfiaFamiglie"></autocomplete>
            </div>
        </div>

        <div v-if="isMaggiorenneSingle || isMaggiorenneConFamiglia" class="form-group row">
            <label  class="col-form-label col-sm-6" for="gruppo">Gruppo Familiare:</label>
            <div class="col-sm-6">
                <autocomplete placeholder="Inserisci Gruppo Familiare..." name="gruppo_id" :url="this.apiNomadelfiaGruppi"></autocomplete>
            </div>
        </div>
</div>
</template>

<script>
import {it} from 'vuejs-datepicker/dist/locale'
import vSelect from "vue-select"
	
export default {
	components: {vSelect},
    props: [
		'apiNomadelfiaFamiglie',
        'apiNomadelfiaPersona',
        'apiNomadelfiaGruppi'
	],
    data(){
        return {
            persona: null,                   // info della persona
           // bodyPersonaEntrata: {            // oggetto con le info da inserire
            //    data_entrata: null
           // },                  
            tipologiaEntrata: null,          // tipologia di entrata della persona
            optionsFamiglie: [],             // list of the famiglie to be shown in the dropdown menu
}
    },
    created: function(){
           axios.get(this.apiNomadelfiaPersona).then(response => {
                this.persona = response.data;
			});
    },
    computed:{
        isNatoANomadelfia: function() {
            return this.tipologiaEntrata == "dalla_nascita";
        },
        isMinorenneAccolto: function() {
            return this.tipologiaEntrata == "minorenne_accolto";
        },
        isMinorenneConFamiglia: function() {
            return this.tipologiaEntrata == "minorenne_famiglia";
        },
        isMaggiorenneSingle: function() {
            return this.tipologiaEntrata == "maggiorenne_single";
        },
        isMaggiorenneConFamiglia: function() {
            return this.tipologiaEntrata == "maggiorenne_famiglia";
        }
    },
    methods: {
        selectDataEntrata: function(data){
                this.persona.data_entrata = this.customFormatter(data);
        },
        customFormatter(date) {
            return moment(date).format('YYYY-MM-DD');
        },
    }
}
</script>


